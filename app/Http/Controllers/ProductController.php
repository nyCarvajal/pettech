<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name')->paginate(15);
        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Product::create($data);
        return redirect()->route('products.index')->with('status', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request, $product->id);
        $product->update($data);
        return redirect()->route('products.index')->with('status', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('status', 'Producto eliminado.');
    }

    public function kardex(Product $product)
    {
        $movements = StockMovement::with('warehouse')
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
                $runningBalance = Stock::where('product_id', $movement->product_id)
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
            'sku' => ['required', 'string', 'max:60', 'unique:products,sku,'.($productId ?? 'NULL').',id'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:20'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'is_service' => ['nullable', 'boolean'],
        ]) + ['is_service' => $request->boolean('is_service')];
    }
}
