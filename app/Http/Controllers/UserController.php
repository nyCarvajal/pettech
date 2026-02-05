<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(12);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'tenant_id' => auth()->user()?->tenant_id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync($this->pivotData($validated['roles'] ?? []));
        $auditLogger->log(auth()->user(), 'create', 'user', $user->id, $validated, $request->ip());

        return redirect()->route('users.index')->with('status', 'Usuario creado');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->roles()->sync($this->pivotData($validated['roles'] ?? []));

        $auditLogger->log(auth()->user(), 'update', 'user', $user->id, $validated, $request->ip());

        return redirect()->route('users.index')->with('status', 'Usuario actualizado');
    }

    public function destroy(User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $user->delete();
        $auditLogger->log(auth()->user(), 'delete', 'user', $user->id, [], request()->ip());

        return redirect()->route('users.index')->with('status', 'Usuario eliminado');
    }

    public function toggleStatus(User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $user->update(['is_active' => ! $user->is_active]);
        $auditLogger->log(auth()->user(), 'toggle_status', 'user', $user->id, ['is_active' => $user->is_active], request()->ip());

        return redirect()->route('users.index')->with('status', 'Estado actualizado');
    }

    private function pivotData(array $roleIds): array
    {
        return collect($roleIds)->mapWithKeys(fn ($roleId) => [
            $roleId => [
                'tenant_id' => auth()->user()?->tenant_id,
                'created_by' => auth()->id(),
                'deleted_at' => null,
            ],
        ])->all();
    }
}
