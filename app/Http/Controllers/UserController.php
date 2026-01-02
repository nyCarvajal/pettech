<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        $auditLogger->log(auth()->user(), 'create', 'user', $user->id, $validated, $request->ip());

        return redirect()->route('users.index')->with('status', 'Usuario creado');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'is_active' => ['boolean'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles($validated['roles'] ?? []);

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
}
