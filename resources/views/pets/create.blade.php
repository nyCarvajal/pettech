@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Mascotas</p>
        <h1>Nueva mascota</h1>
        <p class="muted">Asocia esta mascota al cliente {{ $client->name }}.</p>
    </div>
    <a class="btn btn--ghost" href="{{ route('clients.show', $client) }}">Volver al cliente</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('clients.pets.store', $client) }}">
        @include('pets._form')
    </form>
</div>
@endsection
