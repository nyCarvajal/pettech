@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">CRM</p>
        <h1>Editar cliente</h1>
        <p class="muted">{{ $client->name }}</p>
    </div>
</div>
<div class="card">
    <form class="form" method="POST" action="{{ route('clients.update', $client) }}">
        @method('PUT')
        @include('clients._form', ['client' => $client])
    </form>
</div>
@endsection
