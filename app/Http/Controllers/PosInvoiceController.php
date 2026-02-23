<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pos\AddInvoiceItemRequest;
use App\Http\Requests\Pos\StoreInvoiceRequest;
use App\Http\Requests\Pos\StorePaymentRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Services\InvoiceCalculator;
use App\Services\NumberingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosInvoiceController extends Controller
{
    public function create()
    {
        $this->ensureTenantDatabaseLoaded();

        $tenantId = $this->tenantId();

        $clients = Client::on('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $pets = Pet::on('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('pos.invoices.create', compact('clients', 'pets'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $tenantId = $this->tenantId();
        $userId = Auth::id();

        $invoice = Invoice::query()->create([
            'tenant_id' => $tenantId,
            'created_by' => $userId,
            'client_id' => $request->input('customer_id'),
            'customer_id' => $request->input('customer_id'),
            'pet_id' => $request->input('pet_id'),
            'invoice_number' => 'draft-' . Str::uuid(),
            'invoice_type' => 'pos',
            'status' => 'draft',
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
            'grand_total' => 0,
            'notes' => $request->input('notes'),
            'currency' => 'COP',
        ]);

        return redirect()->route('pos.invoices.show', $invoice);
    }

    public function show(Request $request, Invoice $invoice)
    {
        $this->assertTenant($invoice);

        $search = $request->string('search')->toString();
        $tenantId = $this->tenantId();

        $products = Product::on('tenant')
            ->where('tenant_id', $tenantId)
            ->when($search, fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->orderBy('name')
            ->limit(15)
            ->get();

        $invoice->load(['items.product', 'posPayments']);

        return view('pos.invoices.show', [
            'invoice' => $invoice,
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function addItem(AddInvoiceItemRequest $request, Invoice $invoice, InvoiceCalculator $calculator)
    {
        $this->assertTenant($invoice);

        $product = null;
        if ($request->filled('product_id')) {
            $product = Product::on('tenant')->where('tenant_id', $this->tenantId())->findOrFail($request->input('product_id'));
        }

        $qty = (float) $request->input('qty');
        $unitPrice = $product ? (float) $product->sale_price : (float) $request->input('unit_price');
        $taxRate = $product ? (float) $product->tax_rate : (float) $request->input('tax_rate', 0);
        $description = $product ? $product->name : $request->input('description');
        $isService = $product ? (bool) $product->is_service : (bool) $request->boolean('is_service');

        $line = $calculator->calculateLine($qty, $unitPrice, $taxRate);

        $invoice->items()->create([
            'tenant_id' => $invoice->tenant_id,
            'created_by' => Auth::id(),
            'invoice_id' => $invoice->id,
            'item_type' => $isService ? 'service' : 'product',
            'product_id' => $product?->id,
            'description' => $description,
            'quantity' => $qty,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'discount_rate' => 0,
            'line_total' => $line['line_total'],
            'is_service' => $isService,
        ]);

        $this->recalculateTotals($invoice, $calculator);

        return redirect()->route('pos.invoices.show', $invoice);
    }

    public function storePayment(StorePaymentRequest $request, Invoice $invoice)
    {
        $this->assertTenant($invoice);

        Payment::query()->create([
            'tenant_id' => $invoice->tenant_id,
            'created_by' => Auth::id(),
            'invoice_id' => $invoice->id,
            'method' => $request->input('method'),
            'amount' => $request->input('amount'),
            'paid_at' => $request->date('paid_at'),
            'reference' => $request->input('reference'),
        ]);

        return redirect()->route('pos.invoices.show', $invoice);
    }

    public function issue(
        Invoice $invoice,
        InvoiceCalculator $calculator,
        NumberingService $numberingService,
        InventoryService $inventoryService
    ) {
        $this->assertTenant($invoice);

        if ($invoice->status === 'issued') {
            return redirect()->route('pos.invoices.show', $invoice)->with('status', 'La factura ya fue emitida.');
        }

        $this->recalculateTotals($invoice, $calculator);

        if (!$invoice->number) {
            $invoice->number = $numberingService->nextNumber($invoice->tenant_id, 'pos_invoice');
            $invoice->invoice_number = $invoice->number;
        }

        $invoice->status = 'issued';
        $invoice->issued_at = now();
        $invoice->grand_total = $invoice->total;
        $invoice->save();

        if (!$invoice->inventory_applied_at) {
            $warehouse = Warehouse::on('tenant')
                ->where('tenant_id', $invoice->tenant_id)
                ->orderByDesc('is_main')
                ->first();

            if (!$warehouse) {
                return redirect()->route('pos.invoices.show', $invoice)->with('status', 'No hay bodega configurada para descontar inventario.');
            }

            $invoice->load('items.product');
            foreach ($invoice->items as $item) {
                if (!$item->product || $item->is_service) {
                    continue;
                }

                $qty = (float) ($item->qty ?? $item->quantity ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $inventoryService->decrease(
                    $item->product,
                    $warehouse->id,
                    $qty,
                    'Salida por factura POS',
                    Invoice::class,
                    $invoice->id
                );
            }

            $invoice->inventory_applied_at = now();
            $invoice->save();
        }

        return redirect()->route('pos.invoices.show', $invoice)->with('status', 'Factura emitida.');
    }

    public function print(Invoice $invoice)
    {
        $this->assertTenant($invoice);

        $invoice->load(['items.product', 'customer', 'pet', 'posPayments']);

        return view('pos.invoices.print', ['invoice' => $invoice]);
    }

    public function pdf(Invoice $invoice)
    {
        $this->assertTenant($invoice);

        if (class_exists(Pdf::class)) {
            $invoice->load(['items.product', 'customer', 'pet', 'posPayments']);

            return Pdf::loadView('pos.invoices.print', ['invoice' => $invoice])
                ->download('factura-' . ($invoice->number ?? $invoice->id) . '.pdf');
        }

        return response('PDF renderer no disponible.', 501);
    }

    private function recalculateTotals(Invoice $invoice, InvoiceCalculator $calculator): void
    {
        $invoice->load('items');
        $totals = $calculator->calculateTotals($invoice->items);

        $invoice->subtotal = $totals['subtotal'];
        $invoice->tax_total = $totals['tax_total'];
        $invoice->total = $totals['total'];
        $invoice->grand_total = $totals['total'];
        $invoice->save();
    }

    private function tenantId(): int
    {
        return (int) Auth::user()->tenant_id;
    }

    private function assertTenant(Invoice $invoice): void
    {
        if ((int) $invoice->tenant_id !== $this->tenantId()) {
            abort(404);
        }
    }

    private function ensureTenantDatabaseLoaded(): void
    {
        if (filled(DB::connection('tenant')->getDatabaseName())) {
            return;
        }

        $user = Auth::user();

        if ($user && filled($user->db)) {
            config(['database.connections.tenant.database' => $user->db]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        abort_if(blank(DB::connection('tenant')->getDatabaseName()), 500, 'No se pudo inicializar la base de datos tenant.');
    }
}
