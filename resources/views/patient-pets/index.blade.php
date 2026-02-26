@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Mascotas</h1>
        <a href="{{ route('patient-pets.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nueva mascota</a>
    </div>
    <form class="flex gap-2">
        <input name="search" value="{{ $search }}" class="w-full rounded border-gray-300" placeholder="Buscar por mascota, especie o tutor">
        <button class="rounded border px-3">Buscar</button>
    </form>
    <div class="rounded border bg-white">
        @foreach($pets as $pet)
            <div class="flex items-center justify-between border-b p-3">
                <div>
                    <p class="font-medium">{{ $pet->name }} <span class="text-sm text-gray-500">({{ $pet->species }})</span></p>
                    <p class="text-sm text-gray-500">Tutores: {{ $pet->tutors->pluck('name')->join(', ') }}</p>
                </div>
                <a class="text-blue-600" href="{{ route('patient-pets.show', $pet) }}">Detalle</a>
            </div>
        @endforeach
    </div>
    {{ $pets->links() }}
</div>
@endsection
