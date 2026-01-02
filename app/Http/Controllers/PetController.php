<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pet;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function create(Client $client)
    {
        return view('pets.create', compact('client'));
    }

    public function store(Request $request, Client $client, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $this->validatePet($request);

        $pet = $client->pets()->create(array_merge($validated, [
            'active' => $request->boolean('active', true),
        ]));

        $auditLogger->log(auth()->user(), 'create', 'pet', $pet->id, $validated, $request->ip());

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Mascota creada correctamente');
    }

    public function show(Pet $pet)
    {
        $pet->load('client');

        return view('pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        $pet->load('client');

        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, Pet $pet, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $this->validatePet($request);

        $pet->fill(array_merge($validated, [
            'active' => $request->boolean('active', true),
        ]));

        $pet->save();

        $auditLogger->log(auth()->user(), 'update', 'pet', $pet->id, $validated, $request->ip());

        return redirect()
            ->route('pets.show', $pet)
            ->with('status', 'Mascota actualizada');
    }

    public function destroy(Pet $pet, AuditLogger $auditLogger): RedirectResponse
    {
        $petId = $pet->id;
        $client = $pet->client;
        $pet->delete();

        $auditLogger->log(auth()->user(), 'delete', 'pet', $petId, [], request()->ip());

        return redirect()->route('clients.show', $client)->with('status', 'Mascota eliminada');
    }

    protected function validatePet(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['nullable', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date'],
            'sex' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'behavior_notes' => ['nullable', 'string'],
            'grooming_preferences' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);
    }
}
