<?php

namespace App\Policies;

use App\Models\InventoryMovement;
use App\Models\User;

class InventoryMovementPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('inventario.view'); }
    public function view(User $user, InventoryMovement $movement): bool { return $user->tenant_id === $movement->tenant_id && $user->hasPermission('inventario.view'); }
    public function create(User $user): bool { return $user->hasPermission('inventario.create'); }
    public function update(User $user, InventoryMovement $movement): bool { return $user->tenant_id === $movement->tenant_id && $user->hasPermission('inventario.update'); }
    public function delete(User $user, InventoryMovement $movement): bool { return $user->tenant_id === $movement->tenant_id && $user->hasPermission('inventario.move'); }
}
