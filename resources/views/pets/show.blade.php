@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Mascotas</p>
        <h1>{{ $pet->name }}</h1>
        <p class="muted">Cliente: {{ $pet->client->name }}</p>
    </div>
    <div class="actions">
        <a class="btn btn--ghost" href="{{ route('clients.show', $pet->client) }}">Volver al cliente</a>
        <a class="btn btn--primary" href="{{ route('pets.edit', $pet) }}">Editar</a>
    </div>
</div>

<div class="grid grid--2">
    <div class="card stack">
        <div class="stack">
            <div class="badge {{ $pet->active ? '' : 'badge--ghost' }}">{{ $pet->active ? 'Activa' : 'Inactiva' }}</div>
            <p class="muted">Especie: {{ $pet->species ?: 'No especificada' }}</p>
            <p class="muted">Raza: {{ $pet->breed ?: 'No registrada' }}</p>
            <p class="muted">Tamaño: {{ $pet->size ?: 'No registrado' }}</p>
            <p class="muted">Sexo: {{ $pet->sex ?: 'No registrado' }}</p>
            <p class="muted">Color: {{ $pet->color ?: 'No registrado' }}</p>
            <p class="muted">Nacimiento: {{ $pet->birthdate?->format('d/m/Y') ?: 'Sin fecha' }}</p>
        </div>
        @if($pet->allergies || $pet->behavior_notes)
            <div class="divider"></div>
            <div class="stack">
                @if($pet->allergies)
                    <div class="stack">
                        <p class="eyebrow">Alergias</p>
                        <p>{{ $pet->allergies }}</p>
                    </div>
                @endif
                @if($pet->behavior_notes)
                    <div class="stack">
                        <p class="eyebrow">Notas de comportamiento</p>
                        <p>{{ $pet->behavior_notes }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
    <div class="card stack">
        <div class="stack">
            <p class="eyebrow">Preferencias de grooming</p>
            <p>{{ $pet->grooming_preferences ?: 'Sin preferencias registradas.' }}</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="tabs" data-tabs>
        <div class="tabs__list">
            <button class="tab is-active" type="button" data-tab-target="appointments">Citas</button>
            <button class="tab" type="button" data-tab-target="purchases">Compras</button>
        </div>
        <div class="tabs__panels">
            <div class="tab-panel is-active stack" id="appointments" data-tab-panel>
                <div class="stack">
                    <div class="stack">
                        <p class="eyebrow">Historial</p>
                        <p class="muted">Registra y consulta todas las citas de grooming y bienestar.</p>
                    </div>
                </div>
                <p class="muted">No hay citas registradas para esta mascota.</p>
            </div>
            <div class="tab-panel stack" id="purchases" data-tab-panel>
                <div class="stack">
                    <div class="stack">
                        <p class="eyebrow">Historial</p>
                        <p class="muted">Compras asociadas a productos o servicios de la mascota.</p>
                    </div>
                </div>
                <p class="muted">Aún no se registran compras para esta mascota.</p>
            </div>
        </div>
    </div>
</div>
@endsection
