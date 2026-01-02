<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

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

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['nullable', 'string'],
        ]);

        $permission = Permission::create([
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

    public function update(Request $request, Permission $permission, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'description' => ['nullable', 'string'],
        ]);

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
