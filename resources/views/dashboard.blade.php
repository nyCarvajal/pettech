@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Inicio</p>
        <h1>Dashboard</h1>
    </div>
</div>
<div class="grid grid--3">
    <div class="card">
        <div class="card__header"><p class="text-muted">Usuarios activos</p></div>
        <div class="stat">48</div>
    </div>
    <div class="card">
        <div class="card__header"><p class="text-muted">Roles</p></div>
        <div class="stat">6</div>
    </div>
    <div class="card">
        <div class="card__header"><p class="text-muted">Permisos</p></div>
        <div class="stat">24</div>
    </div>
</div>
@endsection
