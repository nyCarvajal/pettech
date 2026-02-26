@extends('layouts.app')
@section('content')
<h1 class="mb-4 text-xl font-semibold">Nueva mascota</h1>
<form method="POST" action="{{ route('patient-pets.store') }}" class="rounded border bg-white p-4">
    @include('patient-pets._form')
</form>
@endsection
