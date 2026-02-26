<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->tenant_id;
    }

    public function view(User $user, Pet $pet): bool
    {
        return (int) $user->tenant_id === (int) $pet->tenant_id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->tenant_id;
    }

    public function update(User $user, Pet $pet): bool
    {
        return (int) $user->tenant_id === (int) $pet->tenant_id;
    }

    public function delete(User $user, Pet $pet): bool
    {
        return (int) $user->tenant_id === (int) $pet->tenant_id;
    }
}
