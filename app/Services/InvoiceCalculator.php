<?php

namespace App\Services;

use App\Models\InvoiceItem;

class InvoiceCalculator
{
    public function calculateLine(float $qty, float $unitPrice, float $taxRate): array
    {
        $subtotal = $qty * $unitPrice;
        $taxAmount = $subtotal * ($taxRate / 100);
        $lineTotal = $subtotal + $taxAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'line_total' => round($lineTotal, 2),
        ];
    }

    public function calculateTotals(iterable $items): array
    {
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $total = 0.0;

        foreach ($items as $item) {
            $qty = $this->resolveQty($item);
            $unitPrice = (float) ($item->unit_price ?? $item['unit_price'] ?? 0);
            $taxRate = (float) ($item->tax_rate ?? $item['tax_rate'] ?? 0);

            $line = $this->calculateLine($qty, $unitPrice, $taxRate);
            $subtotal += $line['subtotal'];
            $taxTotal += $line['tax_amount'];
            $total += $line['line_total'];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'tax_total' => round($taxTotal, 2),
            'total' => round($total, 2),
        ];
    }

    private function resolveQty(InvoiceItem|array $item): float
    {
        $qty = $item->qty ?? $item->quantity ?? $item['qty'] ?? $item['quantity'] ?? 0;

        return (float) $qty;
    }
}
