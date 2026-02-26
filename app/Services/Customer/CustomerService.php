<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\User;

class CustomerService
{
    public function create(array $data, User $user): Customer
    {
        return Customer::create(array_merge($data, [
            'tenant_id' => $user->tenant_id,
            'created_by' => $user->id,
        ]));
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer;
    }
}
