<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use RuntimeException;

class StockMovementController extends Controller
{
    public function create()
    {
        return view('inventory.movements.create', [
            'products' => Product::where('is_service', false)->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'movement_type' => ['required', 'in:in,out,adjustment'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
            'authorized_adjustment' => ['nullable', 'boolean'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        try {
            match ($data['movement_type']) {
                'in' => $inventoryService->increase($product, (int)$data['warehouse_id'], (float)$data['qty'], $data['reason'] ?? 'Entrada manual', 'manual', null),
                'out' => $inventoryService->decrease($product, (int)$data['warehouse_id'], (float)$data['qty'], $data['reason'] ?? 'Salida manual', 'manual', null),
                default => $inventoryService->adjust(
                    $product,
                    (int)$data['warehouse_id'],
                    (float)$data['qty'],
                    $data['reason'] ?? 'Ajuste manual',
                    (bool)($data['authorized_adjustment'] ?? false)
                ),
            };
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['qty' => $e->getMessage()]);
        }

        return redirect()->route('stock.movements.create')->with('status', 'Movimiento registrado.');
    }

    public function lowStock()
    {
        $rows = Product::query()
            ->where('is_service', false)
            ->with('stocks')
            ->get()
            ->filter(function (Product $product) {
                return $product->stocks->sum('qty') <= $product->min_stock;
            });

        return view('inventory.low-stock', ['products' => $rows]);
    }
}
