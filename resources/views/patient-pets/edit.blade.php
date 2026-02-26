@extends('layouts.app')
@section('content')
<h1 class="mb-4 text-xl font-semibold">Editar mascota</h1>
<form method="POST" action="{{ route('patient-pets.update', $pet) }}" class="rounded border bg-white p-4">
    @method('PUT')
    @include('patient-pets._form', ['pet' => $pet])
</form>
@endsection
