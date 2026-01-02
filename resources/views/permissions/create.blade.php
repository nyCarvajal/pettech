@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Seguridad</p>
        <h1>Crear permiso</h1>
    </div>
</div>
<div class="card">
    <form class="form" method="POST" action="{{ route('permissions.store') }}">
        @csrf
        <label class="form__field">
            <span>Nombre</span>
            <input type="text" class="input" name="name" value="{{ old('name') }}" required>
            @error('name')<span class="form__error">{{ $message }}</span>@enderror
        </label>
        <label class="form__field">
            <span>Descripción</span>
            <input type="text" class="input" name="description" value="{{ old('description') }}">
        </label>
        <div class="form__actions">
            <a class="btn btn--ghost" href="{{ route('permissions.index') }}">Cancelar</a>
            <button class="btn btn--primary" type="submit">Guardar</button>
        </div>
    </form>
</div>
@endsection
