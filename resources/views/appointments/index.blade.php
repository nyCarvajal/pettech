@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Agenda / Citas</h1>
            <p class="text-sm text-gray-500">Vista semanal y lista del día.</p>
        </div>
        <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Nueva cita</a>
    </div>

    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 bg-white p-4 rounded shadow">
        <input type="date" name="date" value="{{ $date }}" class="rounded border-gray-300">
        <select name="groomer_id" class="rounded border-gray-300">
            <option value="">Peluquero</option>
            @foreach($groomers as $groomer)
                <option value="{{ $groomer->id }}" @selected(($filters['groomer_id'] ?? '') == $groomer->id)>{{ $groomer->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border-gray-300">
            <option value="">Estado</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
        <select name="service_type" class="rounded border-gray-300">
            <option value="">Servicio</option>
            @foreach($serviceTypes as $serviceType)
                <option value="{{ $serviceType }}" @selected(($filters['service_type'] ?? '') === $serviceType)>{{ ucfirst($serviceType) }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-gray-900 text-white rounded">Filtrar</button>
    </form>

    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-3">Calendario semanal</h2>
        <div class="grid grid-cols-1 md:grid-cols-7 gap-3">
            @foreach($weekDays as $day)
                <div class="border rounded p-2">
                    <p class="font-medium">{{ $day->translatedFormat('D d/m') }}</p>
                    <div class="mt-2 space-y-2">
                        @forelse($weekAppointments->get($day->toDateString(), collect()) as $item)
                            <div class="text-xs p-2 rounded bg-indigo-50 border border-indigo-100">
                                <p class="font-semibold">{{ $item->start_at?->format('H:i') }} · {{ $item->pet?->name }}</p>
                                <p>{{ ucfirst($item->service_type) }} / {{ $item->assignedTo?->name ?? 'Sin asignar' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Sin citas</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-3">Lista del día</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Hora</th>
                        <th>Code</th>
                        <th>Tutor</th>
                        <th>Mascota</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Peluquero</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dayAppointments as $appointment)
                        <tr class="border-b">
                            <td class="py-2">{{ $appointment->start_at?->format('H:i') }} - {{ $appointment->end_at?->format('H:i') }}</td>
                            <td>{{ $appointment->code }}</td>
                            <td>{{ $appointment->customer?->name }}</td>
                            <td>{{ $appointment->pet?->name }}</td>
                            <td>{{ ucfirst($appointment->service_type) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</td>
                            <td>{{ $appointment->assignedTo?->name ?? 'Sin asignar' }}</td>
                            <td class="py-2">
                                <div class="flex gap-1">
                                    <a href="{{ route('appointments.edit', $appointment) }}" class="px-2 py-1 border rounded">Editar</a>
                                    <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                                        @csrf
                                        <button class="px-2 py-1 border rounded">Confirmar</button>
                                    </form>
                                    <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                        @csrf
                                        <button class="px-2 py-1 border rounded">Cancelar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center text-gray-500">Sin citas para la fecha seleccionada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $dayAppointments->links() }}</div>
    </div>
</div>
@endsection
