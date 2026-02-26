@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between">
        <h1 class="text-xl font-semibold">Tutores</h1>
        <a href="{{ route('customers.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nuevo tutor</a>
    </div>
    <form class="flex gap-2">
        <input name="search" value="{{ $search }}" placeholder="Buscar por nombre o teléfono" class="w-full rounded border-gray-300">
        <button class="rounded border px-3">Buscar</button>
    </form>
    <div class="rounded border bg-white">
        @foreach($customers as $customer)
            <div class="flex items-center justify-between border-b p-3">
                <div>
                    <p class="font-medium">{{ $customer->full_name }}</p>
                    <p class="text-sm text-gray-500">{{ $customer->phone }}</p>
                </div>
                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600">Detalle</a>
            </div>
        @endforeach
    </div>
    {{ $customers->links() }}
</div>
@endsection
