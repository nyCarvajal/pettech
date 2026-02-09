<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\InventoryService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class InventoryServiceTest extends TestCase
{
    public function test_decrease_throws_when_qty_is_invalid(): void
    {
        $service = new InventoryService();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('La cantidad debe ser mayor que cero.');

        $service->decrease(new Product(), 1, 0);
    }
}
