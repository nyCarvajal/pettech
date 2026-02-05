<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(20);

        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(StorePermissionRequest $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validated();

        $permission = Permission::create([
            'tenant_id' => auth()->user()?->tenant_id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'guard_name' => 'web',
            'description' => $validated['description'] ?? null,
        ]);

        $auditLogger->log(auth()->user(), 'create', 'permission', $permission->id, $validated, $request->ip());

        return redirect()->route('permissions.index')->with('status', 'Permiso creado');
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validated();

        $permission->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $auditLogger->log(auth()->user(), 'update', 'permission', $permission->id, $validated, $request->ip());

        return redirect()->route('permissions.index')->with('status', 'Permiso actualizado');
    }

    public function destroy(Permission $permission, AuditLogger $auditLogger): RedirectResponse
    {
        $permission->delete();
        $auditLogger->log(auth()->user(), 'delete', 'permission', $permission->id, [], request()->ip());

        return redirect()->route('permissions.index')->with('status', 'Permiso eliminado');
    }
}
