<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();
        $search = $request->input('search');
        $status = $request->input('status');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('active', true);
        }

        if ($status === 'inactive') {
            $query->where('active', false);
        }

        $clients = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'document' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $client = Client::create(array_merge($validated, [
            'active' => $request->boolean('active', true),
        ]));

        $auditLogger->log(auth()->user(), 'create', 'client', $client->id, $validated, $request->ip());

        return redirect()->route('clients.index')->with('status', 'Cliente creado correctamente');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'document' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $client->fill(array_merge($validated, [
            'active' => $request->boolean('active', true),
        ]));

        $client->save();

        $auditLogger->log(auth()->user(), 'update', 'client', $client->id, $validated, $request->ip());

        return redirect()->route('clients.index')->with('status', 'Cliente actualizado');
    }

    public function destroy(Client $client, AuditLogger $auditLogger): RedirectResponse
    {
        $clientId = $client->id;
        $client->delete();

        $auditLogger->log(auth()->user(), 'delete', 'client', $clientId, [], request()->ip());

        return redirect()->route('clients.index')->with('status', 'Cliente eliminado');
    }
}
