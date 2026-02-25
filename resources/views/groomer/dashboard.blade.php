@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Dashboard Groomer</h1>
            <p class="text-sm text-gray-500">Mis citas grooming del día con seguimiento por etapas.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" class="rounded border-gray-300">
            <button class="px-3 py-2 rounded bg-gray-900 text-white text-sm">Ver</button>
        </form>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Mis citas de hoy</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b text-left">
                        <th class="py-2">Hora</th>
                        <th>Tutor</th>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Notas</th>
                        <th>Etapa</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        @php($session = $appointment->groomingSession)
                        @php($stageIndex = array_search($session?->current_stage, $stages, true))
                        <tr class="border-b align-top">
                            <td class="py-2 whitespace-nowrap">{{ $appointment->start_at?->format('H:i') }} - {{ $appointment->end_at?->format('H:i') }}</td>
                            <td>{{ $appointment->customer?->name }}</td>
                            <td>{{ $appointment->pet?->name }}</td>
                            <td>{{ ucfirst($appointment->service_type) }}</td>
                            <td class="max-w-xs">{{ $appointment->notes ?: '—' }}</td>
                            <td>
                                <span class="inline-flex px-2 py-1 rounded bg-indigo-100 text-indigo-700 text-xs font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $session?->current_stage ?? 'received')) }}
                                </span>
                                <div class="mt-1 text-xs text-gray-500">
                                    @if($session?->stageLogs?->isNotEmpty())
                                        {{ optional($session->stageLogs->sortByDesc('changed_at')->first()->changed_at)->format('H:i') }}
                                    @else
                                        Sin registro
                                    @endif
                                </div>
                            </td>
                            <td class="py-2">
                                <div class="flex gap-1">
                                    <form method="POST" action="{{ route('groomer.dashboard.stage.rollback', $session) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-2 py-1 border rounded disabled:opacity-50" @disabled($stageIndex === 0)>◀</button>
                                    </form>
                                    <form method="POST" action="{{ route('groomer.dashboard.stage.advance', $session) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-2 py-1 border rounded disabled:opacity-50" @disabled($stageIndex === count($stages) - 1)>▶</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">No tienes citas grooming para esta fecha.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Kanban por etapa</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
            @foreach ($kanban as $stage => $cards)
                <div class="border rounded p-2 bg-gray-50">
                    <h3 class="text-sm font-semibold mb-2">{{ ucfirst($stage) }}</h3>
                    <div class="space-y-2 min-h-16">
                        @forelse ($cards as $appointment)
                            @php($session = $appointment->groomingSession)
                            @php($stageIndex = array_search($session?->current_stage, $stages, true))
                            <div class="rounded border bg-white p-2 text-xs">
                                <p class="font-semibold">{{ $appointment->pet?->name }}</p>
                                <p>{{ $appointment->customer?->name }}</p>
                                <p>{{ $appointment->start_at?->format('H:i') }} · {{ ucfirst($appointment->service_type) }}</p>
                                <p class="text-gray-500 truncate">{{ $appointment->notes ?: 'Sin notas' }}</p>
                                <div class="mt-2 flex gap-1">
                                    <form method="POST" action="{{ route('groomer.dashboard.stage.rollback', $session) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-2 py-1 border rounded disabled:opacity-50" @disabled($stageIndex === 0)>◀</button>
                                    </form>
                                    <form method="POST" action="{{ route('groomer.dashboard.stage.advance', $session) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-2 py-1 border rounded disabled:opacity-50" @disabled($stageIndex === count($stages) - 1)>▶</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Sin pacientes</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
