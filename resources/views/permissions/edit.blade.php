@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Seguridad</p>
        <h1>Editar permiso</h1>
    </div>
</div>
<div class="card">
    <form class="form" method="POST" action="{{ route('permissions.update', $permission) }}">
        @csrf
        @method('PUT')
        <label class="form__field">
            <span>Nombre</span>
            <input type="text" class="input" name="name" value="{{ old('name', $permission->name) }}" required>
            @error('name')<span class="form__error">{{ $message }}</span>@enderror
        </label>
        <label class="form__field">
            <span>Descripción</span>
            <input type="text" class="input" name="description" value="{{ old('description', $permission->description) }}">
        </label>
        <div class="form__actions">
            <a class="btn btn--ghost" href="{{ route('permissions.index') }}">Cancelar</a>
            <button class="btn btn--primary" type="submit">Actualizar</button>
        </div>
    </form>
</div>
@endsection
