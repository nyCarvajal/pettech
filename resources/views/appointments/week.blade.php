@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">Agenda · Semana</h1>
            <p class="text-sm text-gray-500">Vista semanal sin librerías externas.</p>
        </div>
        <a href="{{ route('appointments.day', request()->query()) }}" class="px-4 py-2 border rounded">Ver día</a>
    </div>

    @include('appointments.partials.filters')

    <div class="bg-white p-4 rounded shadow">
        <div class="grid grid-cols-1 md:grid-cols-7 gap-3">
            @foreach($weekDays as $day)
                <div class="border rounded p-2">
                    <p class="font-medium">{{ $day->translatedFormat('D d/m') }}</p>
                    <div class="mt-2 space-y-2">
                        @forelse($weekAppointments->get($day->toDateString(), collect()) as $item)
                            <div class="text-xs p-2 rounded bg-indigo-50 border border-indigo-100">
                                <p class="font-semibold">{{ $item->start_at?->format('H:i') }} · {{ $item->pet?->name ?? 'Mascota' }}</p>
                                <p>{{ ucfirst($item->service_type) }} / {{ $item->assignedTo?->name ?? 'Sin asignar' }}</p>
                                <p>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Sin citas</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
