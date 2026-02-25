<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPendingElectronicInvoiceJob;
use App\Models\ElectronicInvoice;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElectronicInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = (int) Auth::user()->tenant_id;

        $electronicInvoices = ElectronicInvoice::query()
            ->where('tenant_id', $tenantId)
            ->with('invoice')
            ->orderByDesc('id')
            ->paginate(20);

        return view('dian.electronic_invoices.index', compact('electronicInvoices'));
    }

    public function show(Invoice $invoice)
    {
        $this->assertTenant($invoice);

        $electronicInvoice = $invoice->electronicInvoice;

        abort_if(! $electronicInvoice, 404, 'La factura no tiene registro electrónico DIAN.');

        return view('dian.electronic_invoices.show', compact('invoice', 'electronicInvoice'));
    }

    public function retry(Invoice $invoice): RedirectResponse
    {
        $this->assertTenant($invoice);

        $electronicInvoice = $invoice->electronicInvoice;

        abort_if(! $electronicInvoice, 404, 'La factura no tiene registro electrónico DIAN.');

        $electronicInvoice->update([
            'dian_status' => 'pending',
            'last_error' => null,
        ]);

        ProcessPendingElectronicInvoiceJob::dispatch($electronicInvoice->id)->onQueue('dian');

        return back()->with('status', 'Reintento DIAN encolado.');
    }

    private function assertTenant(Invoice $invoice): void
    {
        if ((int) $invoice->tenant_id !== (int) Auth::user()->tenant_id) {
            abort(404);
        }
    }
}
