<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Clinica;
use App\Models\Client;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureTenantDatabaseIsReady($request);

        $date = $request->input('date', now()->toDateString());
        $currentDate = Carbon::parse($date);
        $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        $baseQuery = Appointment::query()
            ->with(['customer', 'pet', 'assignedTo'])
            ->forGroomer($request->integer('groomer_id'))
            ->forStatus($request->input('status'))
            ->forService($request->input('service_type'));

        $weekAppointments = (clone $baseQuery)
            ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
            ->orderBy('start_at')
            ->get()
            ->groupBy(fn (Appointment $appointment) => $appointment->start_at->toDateString());

        $dayAppointments = (clone $baseQuery)
            ->forDate($date)
            ->orderBy('start_at')
            ->paginate(15)
            ->withQueryString();

        return view('appointments.index', [
            'dayAppointments' => $dayAppointments,
            'weekAppointments' => $weekAppointments,
            'weekDays' => collect(range(0, 6))->map(fn ($i) => $startOfWeek->copy()->addDays($i)),
            'date' => $date,
            'filters' => $request->only(['groomer_id', 'status', 'service_type']),
            'groomers' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => Appointment::STATUSES,
            'serviceTypes' => Appointment::SERVICE_TYPES,
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureTenantDatabaseIsReady($request);

        return view('appointments.create', $this->formData());
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['code'] = $data['code'] ?? $this->generateCode();
        $data['tenant_id'] = auth()->user()?->tenant_id;
        $data['created_by'] = auth()->id();
        $data['client_id'] = $data['customer_id'];

        Appointment::create($data);

        return redirect()->route('appointments.index')->with('status', 'Cita creada correctamente.');
    }

    public function edit(Request $request, Appointment $appointment): View
    {
        $this->ensureTenantDatabaseIsReady($request);

        return view('appointments.edit', array_merge($this->formData(), compact('appointment')));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();
        $data['client_id'] = $data['customer_id'];

        $appointment->update($data);

        return redirect()->route('appointments.index')->with('status', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('status', 'Cita eliminada.');
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'confirmed']);

        return back()->with('status', 'Cita confirmada.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'status' => 'cancelled',
            'notes' => trim(($appointment->notes ? $appointment->notes.PHP_EOL : '').'Cancelada: '.($request->input('reason', 'sin motivo especificado'))),
        ]);

        return back()->with('status', 'Cita cancelada.');
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

    private function generateCode(): string
    {
        return 'APT-'.now()->format('Ymd-His').'-'.strtoupper(substr((string) str()->uuid(), 0, 6));
    }

    private function ensureTenantDatabaseIsReady(Request $request): void
    {
        $user = $request->user();
        abort_unless($user, 403, 'Usuario no autenticado.');

        $clinica = Clinica::resolveForUser($user);
        $database = collect([
            $clinica?->db,
            $user->db ?? null,
            data_get($user, 'tenant.tenancy_db_name'),
            data_get($user, 'tenant.database'),
            data_get($user, 'tenant.data.database'),
            config('database.connections.tenant.database'),
            config('database.connections.mysql.database'),
        ])
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->map(fn (string $value) => trim($value))
            ->first();

        if (! is_string($database)) {
            return;
        }

        try {
            Config::set('database.connections.tenant.database', $database);
            DB::purge('tenant');
            DB::reconnect('tenant');
            DB::setDefaultConnection('tenant');

            $activeDb = DB::connection('tenant')->getDatabaseName();
            if (! $activeDb) {
                Config::set('database.connections.tenant.database', config('database.connections.mysql.database'));
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
