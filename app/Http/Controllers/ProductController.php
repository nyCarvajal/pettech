<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $this->ensureTenantDatabaseLoaded();

        $products = Product::on('tenant')->with('category')->orderBy('name')->paginate(15);

        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        $this->ensureTenantDatabaseLoaded();

        $categories = Category::on('tenant')->orderBy('name')->get();

        return view('inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->ensureTenantDatabaseLoaded();

        $data = $this->validateData($request);
        Product::on('tenant')->create($data);

        return redirect()->route('products.index')->with('status', 'Producto creado.');
    }

    public function edit(int $product)
    {
        $this->ensureTenantDatabaseLoaded();

        $product = Product::on('tenant')->findOrFail($product);
        $categories = Category::on('tenant')->orderBy('name')->get();

        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, int $product)
    {
        $this->ensureTenantDatabaseLoaded();

        $product = Product::on('tenant')->findOrFail($product);
        $data = $this->validateData($request, $product->id);
        $product->update($data);

        return redirect()->route('products.index')->with('status', 'Producto actualizado.');
    }

    public function destroy(int $product)
    {
        $this->ensureTenantDatabaseLoaded();

        $product = Product::on('tenant')->findOrFail($product);
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Producto eliminado.');
    }

    public function kardex(int $product)
    {
        $this->ensureTenantDatabaseLoaded();

        $product = Product::on('tenant')->findOrFail($product);

        $movements = StockMovement::on('tenant')->with('warehouse')
            ->where('product_id', $product->id)
            ->orderBy('created_at')
            ->get();

        $runningBalance = 0;
        $kardex = $movements->map(function (StockMovement $movement) use (&$runningBalance) {
            if ($movement->movement_type === 'in') {
                $runningBalance += $movement->qty;
            } elseif ($movement->movement_type === 'out') {
                $runningBalance -= $movement->qty;
            }

            if ($movement->movement_type === 'adjustment') {
                $runningBalance = Stock::on('tenant')->where('product_id', $movement->product_id)
                    ->where('warehouse_id', $movement->warehouse_id)
                    ->value('qty') ?? $runningBalance;
            }

            $movement->balance = $runningBalance;
            return $movement;
        });

        return view('inventory.products.kardex', compact('product', 'kardex'));
    }

    private function validateData(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:60', 'unique:tenant.products,sku,'.($productId ?? 'NULL').',id'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:tenant.categories,id'],
            'unit' => ['required', 'string', 'max:20'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'is_service' => ['nullable', 'boolean'],
        ]) + ['is_service' => $request->boolean('is_service')];
    }
    private function ensureTenantDatabaseLoaded(): void
    {
        if (filled(DB::connection('tenant')->getDatabaseName())) {
            return;
        }

        $user = Auth::user();

        $database = $this->resolveTenantDatabaseName($user);

        if (filled($database)) {
            config(['database.connections.tenant.database' => $database]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        if (blank(DB::connection('tenant')->getDatabaseName())) {
            Log::error('No se pudo inicializar la base de datos tenant.', [
                'controller' => static::class,
                'user_id' => $user?->id,
                'requested_tenant_db' => $database,
                'configured_tenant_db' => config('database.connections.tenant.database'),
                'route' => request()->path(),
            ]);

            abort(500, 'No se pudo inicializar la base de datos tenant. Revisa storage/logs/laravel.log para más detalles.');
        }
    }

}
