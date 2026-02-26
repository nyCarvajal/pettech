@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">{{ $customer->full_name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="rounded border px-3 py-2">Editar</a>
            <form method="POST" action="{{ route('customers.destroy', $customer) }}">@csrf @method('DELETE')<button class="rounded border px-3 py-2">Eliminar</button></form>
        </div>
    </div>
    <p><strong>Teléfono:</strong> {{ $customer->phone }}</p>
    <p><strong>Email:</strong> {{ $customer->email }}</p>
    <div>
        <h2 class="font-semibold">Mascotas asociadas</h2>
        <ul class="list-disc pl-5">
            @forelse($customer->pets as $pet)
                <li><a class="text-blue-600" href="{{ route('patient-pets.show', $pet) }}">{{ $pet->name }}</a></li>
            @empty
                <li>Sin mascotas asociadas.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
