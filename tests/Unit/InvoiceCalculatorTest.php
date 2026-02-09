<?php

namespace Tests\Unit;

use App\Services\InvoiceCalculator;
use PHPUnit\Framework\TestCase;

class InvoiceCalculatorTest extends TestCase
{
    public function test_calculates_totals_for_items(): void
    {
        $calculator = new InvoiceCalculator();

        $items = [
            ['qty' => 2, 'unit_price' => 100, 'tax_rate' => 10],
            ['qty' => 1, 'unit_price' => 50, 'tax_rate' => 0],
        ];

        $totals = $calculator->calculateTotals($items);

        $this->assertSame(250.0, $totals['subtotal']);
        $this->assertSame(20.0, $totals['tax_total']);
        $this->assertSame(270.0, $totals['total']);
    }
}
