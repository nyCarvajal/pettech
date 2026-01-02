@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Mascotas</p>
        <h1>Editar {{ $pet->name }}</h1>
        <p class="muted">Cliente: {{ $pet->client->name }}</p>
    </div>
    <a class="btn btn--ghost" href="{{ route('pets.show', $pet) }}">Ver detalle</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('pets.update', $pet) }}">
        @method('PUT')
        @include('pets._form')
    </form>
</div>
@endsection
