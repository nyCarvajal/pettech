@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">{{ $pet->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('patient-pets.edit', $pet) }}" class="rounded border px-3 py-2">Editar</a>
            <form method="POST" action="{{ route('patient-pets.destroy', $pet) }}">@csrf @method('DELETE')<button class="rounded border px-3 py-2">Eliminar</button></form>
        </div>
    </div>
    <p><strong>Especie:</strong> {{ $pet->species }}</p>
    <p><strong>Raza:</strong> {{ $pet->breed }}</p>
    <p><strong>Sexo:</strong> {{ $pet->sex }}</p>
    <p><strong>Tutores:</strong></p>
    <ul class="list-disc pl-5">
        @foreach($pet->tutors as $tutor)
            <li>{{ $tutor->name }} ({{ $tutor->pivot->relationship }}) @if($tutor->pivot->is_primary) - Principal @endif</li>
        @endforeach
    </ul>
</div>
@endsection
