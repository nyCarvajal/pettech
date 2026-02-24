<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;

        $permissionsByModule = [
            'acceso' => ['manage users', 'manage roles', 'manage permissions', 'manage clients'],
            'dashboard' => ['dashboard.admin.view', 'dashboard.groomer.view'],
            'clientes' => ['clientes.view', 'clientes.create', 'clientes.update', 'clientes.delete'],
            'mascotas' => ['mascotas.view', 'mascotas.create', 'mascotas.update', 'mascotas.delete'],
            'agenda' => ['agenda.view', 'agenda.create', 'agenda.update', 'agenda.cancel'],
            'inventario' => ['inventario.view', 'inventario.create', 'inventario.update', 'inventario.move'],
            'facturacion' => ['facturacion.view', 'facturacion.create', 'facturacion.void', 'facturacion.dian.send', 'facturacion.dian.view'],
        ];

        $permissionIds = collect($permissionsByModule)
            ->flatten()
            ->mapWithKeys(function (string $permission) use ($tenantId) {
                $model = Permission::query()->updateOrCreate(
                    ['tenant_id' => $tenantId, 'name' => $permission],
                    ['guard_name' => 'web', 'description' => $permission]
                );

                return [$permission => $model->id];
            });

        $roles = [
            'Admin' => $permissionIds->values()->all(),
            'Recepción' => $permissionIds->only([
                'dashboard.admin.view',
                'clientes.view', 'clientes.create', 'clientes.update',
                'mascotas.view', 'mascotas.create', 'mascotas.update',
                'agenda.view', 'agenda.create', 'agenda.update', 'agenda.cancel',
                'facturacion.view', 'facturacion.create', 'facturacion.dian.view',
                'manage clients',
            ])->values()->all(),
            'Peluquero' => $permissionIds->only([
                'dashboard.groomer.view',
                'agenda.view', 'agenda.update',
                'clientes.view',
                'mascotas.view',
            ])->values()->all(),
        ];

        foreach ($roles as $name => $permissions) {
            $role = Role::query()->updateOrCreate(
                ['tenant_id' => $tenantId, 'name' => $name],
                ['guard_name' => 'web', 'description' => "Rol {$name}"]
            );

            $syncData = collect($permissions)
                ->mapWithKeys(fn ($permissionId) => [
                    $permissionId => ['tenant_id' => $tenantId, 'created_by' => null, 'deleted_at' => null],
                ])
                ->all();

            $role->permissions()->sync($syncData);
        }


        $bootstrapAdmin = User::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('id')
            ->first();

        if ($bootstrapAdmin && ! $bootstrapAdmin->hasRole('Admin')) {
            $bootstrapAdmin->assignRole('Admin');
        }
    }
}
