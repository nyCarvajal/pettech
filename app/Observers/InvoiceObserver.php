<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    public function updated(Invoice $invoice): void
    {
        if ($invoice->inventory_applied_at || !in_array($invoice->status, ['issued', 'paid'], true)) {
            return;
        }

        /** @var InventoryService $inventoryService */
        $inventoryService = app(InventoryService::class);

        DB::transaction(function () use ($invoice, $inventoryService) {
            $warehouseId = (int) (request('warehouse_id') ?? 1);

            $invoice->loadMissing('items.product');

            foreach ($invoice->items as $item) {
                if (!$item->product || $item->product->is_service) {
                    continue;
                }

                $inventoryService->decrease(
                    $item->product,
                    $warehouseId,
                    (float) $item->quantity,
                    'Descuento automático por factura',
                    Invoice::class,
                    $invoice->id
                );
            }

            $invoice->forceFill(['inventory_applied_at' => now()])->saveQuietly();
        });
    }
}
