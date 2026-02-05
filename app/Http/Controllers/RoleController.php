<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'tenant_id' => auth()->user()?->tenant_id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'guard_name' => 'web',
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($this->pivotData($validated['permissions'] ?? []));
        $auditLogger->log(auth()->user(), 'create', 'role', $role->id, $validated, $request->ip());

        return redirect()->route('roles.index')->with('status', 'Rol creado');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($this->pivotData($validated['permissions'] ?? []));
        $auditLogger->log(auth()->user(), 'update', 'role', $role->id, $validated, $request->ip());

        return redirect()->route('roles.index')->with('status', 'Rol actualizado');
    }

    public function destroy(Role $role, AuditLogger $auditLogger): RedirectResponse
    {
        $role->delete();
        $auditLogger->log(auth()->user(), 'delete', 'role', $role->id, [], request()->ip());

        return redirect()->route('roles.index')->with('status', 'Rol eliminado');
    }

    private function pivotData(array $permissionIds): array
    {
        return collect($permissionIds)->mapWithKeys(fn ($permissionId) => [
            $permissionId => [
                'tenant_id' => auth()->user()?->tenant_id,
                'created_by' => auth()->id(),
                'deleted_at' => null,
            ],
        ])->all();
    }
}
