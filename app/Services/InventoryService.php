<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function increase(Product $product, int $warehouseId, float $qty, ?string $reason = null, ?string $referenceType = null, ?int $referenceId = null): Stock
    {
        if ($qty <= 0) {
            throw new RuntimeException('La cantidad debe ser mayor que cero.');
        }

        return DB::transaction(function () use ($product, $warehouseId, $qty, $reason, $referenceType, $referenceId) {
            $stock = Stock::query()->firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['qty' => 0]
            );

            $stock->increment('qty', $qty);

            $this->createMovement($product->id, $warehouseId, 'in', $qty, $reason, $referenceType, $referenceId);

            return $stock->refresh();
        });
    }

    public function decrease(Product $product, int $warehouseId, float $qty, ?string $reason = null, ?string $referenceType = null, ?int $referenceId = null): Stock
    {
        if ($qty <= 0) {
            throw new RuntimeException('La cantidad debe ser mayor que cero.');
        }

        return DB::transaction(function () use ($product, $warehouseId, $qty, $reason, $referenceType, $referenceId) {
            $stock = Stock::query()->lockForUpdate()->firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['qty' => 0]
            );

            if ($stock->qty < $qty) {
                throw new RuntimeException('No hay stock suficiente para realizar la salida.');
            }

            $stock->decrement('qty', $qty);

            $this->createMovement($product->id, $warehouseId, 'out', $qty, $reason, $referenceType, $referenceId);

            return $stock->refresh();
        });
    }

    public function adjust(Product $product, int $warehouseId, float $newQty, ?string $reason = null, bool $authorizedByAdmin = false): Stock
    {
        return DB::transaction(function () use ($product, $warehouseId, $newQty, $reason, $authorizedByAdmin) {
            $stock = Stock::query()->lockForUpdate()->firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['qty' => 0]
            );

            if ($newQty < 0 && !$authorizedByAdmin) {
                throw new RuntimeException('Solo un administrador puede ajustar a stock negativo.');
            }

            if ($newQty < 0 && !$this->currentUserIsAdmin()) {
                throw new RuntimeException('Ajuste negativo no autorizado.');
            }

            $difference = $newQty - $stock->qty;
            $stock->update(['qty' => $newQty]);

            if ($difference !== 0.0) {
                $this->createMovement($product->id, $warehouseId, 'adjustment', abs($difference), $reason);
            }

            return $stock->refresh();
        });
    }

    private function createMovement(int $productId, int $warehouseId, string $type, float $qty, ?string $reason, ?string $referenceType = null, ?int $referenceId = null): void
    {
        StockMovement::query()->create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'movement_type' => $type,
            'qty' => $qty,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => Auth::id(),
        ]);
    }

    private function currentUserIsAdmin(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->roles()->whereRaw('LOWER(name) = ?', ['admin'])->exists();
    }
}
