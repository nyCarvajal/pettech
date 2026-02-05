@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-semibold mb-4">Editar cita {{ $appointment->code }}</h1>
    <form method="POST" action="{{ route('appointments.update', $appointment) }}">
        @method('PUT')
        @include('appointments._form', ['appointment' => $appointment])
    </form>
</div>
@endsection
