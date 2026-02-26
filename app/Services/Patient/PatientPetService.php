<?php

namespace App\Services\Patient;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PatientPetService
{
    public function create(array $data, User $user): Pet
    {
        return DB::transaction(function () use ($data, $user) {
            $links = $data['customer_links'] ?? [];
            unset($data['customer_links']);

            $pet = Pet::create(array_merge($data, [
                'tenant_id' => $user->tenant_id,
                'created_by' => $user->id,
                'client_id' => null,
                'active' => true,
            ]));

            $this->syncCustomers($pet, $links, $user);

            return $pet;
        });
    }

    public function update(Pet $pet, array $data, User $user): Pet
    {
        return DB::transaction(function () use ($pet, $data, $user) {
            $links = $data['customer_links'] ?? [];
            unset($data['customer_links']);

            $pet->update($data);
            $this->syncCustomers($pet, $links, $user);

            return $pet;
        });
    }

    private function syncCustomers(Pet $pet, array $links, User $user): void
    {
        $syncData = collect($links)
            ->mapWithKeys(fn (array $link) => [
                (int) $link['customer_id'] => [
                    'tenant_id' => $user->tenant_id,
                    'relationship' => $link['relationship'],
                    'is_primary' => (bool) ($link['is_primary'] ?? false),
                    'created_by' => $user->id,
                ],
            ])->all();

        $pet->customers()->sync($syncData);
    }
}
