@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Agenda · Lista del día</h1>
            <p class="text-sm text-gray-500">Filtra por fecha, groomer, estado y tipo.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('appointments.week', request()->query()) }}" class="px-4 py-2 border rounded">Ver semana</a>
            @can('create', App\Models\Appointment::class)
                <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Nueva cita</a>
            @endcan
        </div>
    </div>

    @include('appointments.partials.filters')

    <div class="bg-white p-4 rounded shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Hora</th>
                        <th>Código</th>
                        <th>Tutor</th>
                        <th>Mascota</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Groomer</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr class="border-b">
                            <td class="py-2">{{ $appointment->start_at?->format('H:i') }} - {{ $appointment->end_at?->format('H:i') }}</td>
                            <td>{{ $appointment->code }}</td>
                            <td>{{ $appointment->customer?->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->pet?->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($appointment->service_type) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</td>
                            <td>{{ $appointment->assignedTo?->name ?? 'Sin asignar' }}</td>
                            <td class="py-2">
                                <div class="flex gap-1 flex-wrap justify-end">
                                    @can('update', $appointment)
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="px-2 py-1 border rounded">Editar</a>
                                        <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">@csrf<button class="px-2 py-1 border rounded">Confirmar</button></form>
                                        <form method="POST" action="{{ route('appointments.start', $appointment) }}">@csrf<button class="px-2 py-1 border rounded">Iniciar</button></form>
                                        <form method="POST" action="{{ route('appointments.finish', $appointment) }}">@csrf<button class="px-2 py-1 border rounded">Finalizar</button></form>
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">@csrf<button class="px-2 py-1 border rounded">Cancelar</button></form>
                                    @endcan
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
        <div class="mt-3">{{ $appointments->links() }}</div>
    </div>
</div>
@endsection
