<?php

namespace Tests\Unit;

use App\Models\Customer;
use PHPUnit\Framework\TestCase;

class CustomerModelTest extends TestCase
{
    public function test_full_name_accessor_concatenates_first_and_last_name(): void
    {
        $customer = new Customer([
            'first_name' => 'Ana',
            'last_name' => 'Gomez',
        ]);

        $this->assertSame('Ana Gomez', $customer->full_name);
    }
}
