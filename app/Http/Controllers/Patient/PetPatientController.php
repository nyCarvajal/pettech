<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientPetRequest;
use App\Http\Requests\Patient\UpdatePatientPetRequest;
use App\Models\Client;
use App\Models\Pet;
use App\Services\Patient\PatientPetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetPatientController extends Controller
{
    public function __construct(private readonly PatientPetService $patientPetService)
    {
        $this->authorizeResource(Pet::class, 'patient_pet');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $pets = Pet::query()
            ->where('tenant_id', $request->user()->tenant_id)
            ->with('tutors')
            ->search($search)
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('patient-pets.index', compact('pets', 'search'));
    }

    public function create(Request $request): View
    {
        $clients = Client::query()
            ->where('tenant_id', $request->user()->tenant_id)
            ->search($request->string('tutor_search')->toString())
            ->orderBy('name')
            ->limit(100)
            ->get();

        return view('patient-pets.create', compact('clients'));
    }

    public function store(StorePatientPetRequest $request): RedirectResponse
    {
        $pet = $this->patientPetService->create($request->validated(), $request->user());

        return redirect()->route('patient-pets.show', $pet)->with('status', 'Mascota creada correctamente');
    }

    public function show(Pet $patient_pet): View
    {
        $patient_pet->load('tutors');

        return view('patient-pets.show', ['pet' => $patient_pet]);
    }

    public function edit(Request $request, Pet $patient_pet): View
    {
        $patient_pet->load('tutors');

        $clients = Client::query()
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->limit(100)
            ->get();

        return view('patient-pets.edit', ['pet' => $patient_pet, 'clients' => $clients]);
    }

    public function update(UpdatePatientPetRequest $request, Pet $patient_pet): RedirectResponse
    {
        $this->patientPetService->update($patient_pet, $request->validated(), $request->user());

        return redirect()->route('patient-pets.show', $patient_pet)->with('status', 'Mascota actualizada');
    }

    public function destroy(Pet $patient_pet): RedirectResponse
    {
        $patient_pet->delete();

        return redirect()->route('patient-pets.index')->with('status', 'Mascota eliminada');
    }
}
