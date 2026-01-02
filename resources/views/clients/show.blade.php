@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">CRM</p>
        <h1>{{ $client->name }}</h1>
        <p class="muted">{{ $client->email ?: 'Sin correo' }} · {{ $client->phone ?: 'Sin teléfono' }}</p>
    </div>
    <div class="actions">
        <a class="btn btn--ghost" href="{{ route('clients.edit', $client) }}">Editar</a>
    </div>
</div>

<div class="grid grid--2">
    <div class="card stack">
        <div class="stack">
            <div class="badge {{ $client->active ? '' : 'badge--ghost' }}">{{ $client->active ? 'Activo' : 'Inactivo' }}</div>
            <p class="muted">Documento: {{ $client->document ?: 'No registrado' }}</p>
            <p class="muted">Dirección: {{ $client->address ?: 'Sin dirección' }}</p>
        </div>
        @if($client->notes)
            <div class="divider"></div>
            <div class="stack">
                <p class="eyebrow">Notas</p>
                <p>{{ $client->notes }}</p>
            </div>
        @endif
    </div>
    <div class="card">
        <div class="tabs" data-tabs>
            <div class="tabs__list">
                <button class="tab is-active" type="button" data-tab-target="pets">Mascotas</button>
                <button class="tab" type="button" data-tab-target="appointments">Citas</button>
                <button class="tab" type="button" data-tab-target="purchases">Compras</button>
            </div>
            <div class="tabs__panels">
                <div class="tab-panel is-active" id="pets" data-tab-panel>
                    <p class="muted">Sin mascotas registradas todavía.</p>
                </div>
                <div class="tab-panel" id="appointments" data-tab-panel>
                    <p class="muted">No hay citas previas para este cliente.</p>
                </div>
                <div class="tab-panel" id="purchases" data-tab-panel>
                    <p class="muted">Aún no se registran compras.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
