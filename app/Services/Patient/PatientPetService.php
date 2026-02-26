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
            $links = $data['tutor_links'] ?? [];
            unset($data['tutor_links']);

            $pet = Pet::create(array_merge($data, [
                'tenant_id' => $user->tenant_id,
                'created_by' => $user->id,
                'active' => true,
            ]));

            $this->syncTutors($pet, $links, $user);

            return $pet;
        });
    }

    public function update(Pet $pet, array $data, User $user): Pet
    {
        return DB::transaction(function () use ($pet, $data, $user) {
            $links = $data['tutor_links'] ?? [];
            unset($data['tutor_links']);

            $pet->update($data);
            $this->syncTutors($pet, $links, $user);

            return $pet;
        });
    }

    private function syncTutors(Pet $pet, array $links, User $user): void
    {
        $syncData = collect($links)
            ->filter(fn (array $link) => isset($link['client_id']))
            ->mapWithKeys(fn (array $link) => [
                (int) $link['client_id'] => [
                    'tenant_id' => $user->tenant_id,
                    'relationship' => $link['relationship'] ?? 'owner',
                    'is_primary' => (bool) ($link['is_primary'] ?? false),
                    'created_by' => $user->id,
                ],
            ])->all();

        $pet->tutors()->sync($syncData);
    }
}
