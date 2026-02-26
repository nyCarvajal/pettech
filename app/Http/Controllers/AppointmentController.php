<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Pet;
use App\Models\User;
use App\Services\Appointment\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointmentService)
    {
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('appointments.day');
    }

    public function day(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $date = $request->input('date', now()->toDateString());
        $query = $this->filteredQuery($request);

        if ($request->user()?->hasRole('groomer')) {
            $query->assignedTo((int) $request->user()->id);
        }

        $appointments = $query
            ->forDate($date)
            ->orderBy('start_at')
            ->paginate(20)
            ->withQueryString();

        return view('appointments.day', [
            'appointments' => $appointments,
            'date' => $date,
            ...$this->viewData($request),
        ]);
    }

    public function week(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $date = Carbon::parse($request->input('date', now()->toDateString()));
        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY);

        $query = $this->filteredQuery($request);

        if ($request->user()?->hasRole('groomer')) {
            $query->assignedTo((int) $request->user()->id);
        }

        $appointments = $query
            ->forDateRange($startOfWeek->toDateTimeString(), $endOfWeek->toDateTimeString())
            ->orderBy('start_at')
            ->get()
            ->groupBy(fn (Appointment $appointment) => $appointment->start_at?->toDateString());

        return view('appointments.week', [
            'weekAppointments' => $appointments,
            'weekDays' => collect(range(0, 6))->map(fn (int $offset) => $startOfWeek->copy()->addDays($offset)),
            'date' => $date->toDateString(),
            ...$this->viewData($request),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Appointment::class);

        return view('appointments.create', $this->formData());
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $this->appointmentService->create($request->validated(), $request->user());

        return redirect()->route('appointments.day')->with('status', 'Cita creada correctamente.');
    }

    public function edit(Appointment $appointment): View
    {
        $this->authorize('update', $appointment);

        return view('appointments.edit', array_merge($this->formData(), compact('appointment')));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->appointmentService->update($appointment, $request->validated(), $request->user());

        return redirect()->route('appointments.day')->with('status', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return redirect()->route('appointments.day')->with('status', 'Cita eliminada.');
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        $this->appointmentService->transition($appointment, 'confirmed');

        return back()->with('status', 'Cita confirmada.');
    }

    public function start(Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        $this->appointmentService->transition($appointment, 'in_progress');

        return back()->with('status', 'Cita iniciada.');
    }

    public function finish(Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        $this->appointmentService->transition($appointment, 'done');

        return back()->with('status', 'Cita finalizada.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $note = 'Cancelada: '.($request->string('reason')->trim()->value() ?: 'sin motivo especificado');
        $this->appointmentService->transition($appointment, 'cancelled', $note);

        return back()->with('status', 'Cita cancelada.');
    }

    private function filteredQuery(Request $request)
    {
        return Appointment::query()
            ->forTenant((int) $request->user()->tenant_id)
            ->with(['customer', 'pet', 'assignedTo'])
            ->forGroomer($request->integer('groomer_id'))
            ->forStatus($request->input('status'))
            ->forService($request->input('service_type'));
    }

    private function formData(): array
    {
        return [
            'customers' => Client::query()->orderBy('name')->get(['id', 'name']),
            'pets' => Pet::query()->orderBy('name')->get(['id', 'name', 'client_id']),
            'groomers' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => Appointment::STATUSES,
            'serviceTypes' => Appointment::SERVICE_TYPES,
        ];
    }

    private function viewData(Request $request): array
    {
        return [
            'filters' => $request->only(['groomer_id', 'status', 'service_type']),
            'groomers' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => Appointment::STATUSES,
            'serviceTypes' => Appointment::SERVICE_TYPES,
        ];
    }
}
