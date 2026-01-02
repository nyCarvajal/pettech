@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Seguridad</p>
        <h1>Crear usuario</h1>
    </div>
</div>
<div class="card">
    <form class="form" method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="grid grid--2">
            <label class="form__field">
                <span>Nombre</span>
                <input type="text" name="name" class="input" value="{{ old('name') }}" required>
                @error('name')<span class="form__error">{{ $message }}</span>@enderror
            </label>
            <label class="form__field">
                <span>Email</span>
                <input type="email" name="email" class="input" value="{{ old('email') }}" required>
                @error('email')<span class="form__error">{{ $message }}</span>@enderror
            </label>
        </div>
        <div class="grid grid--2">
            <label class="form__field">
                <span>Contraseña</span>
                <input type="password" name="password" class="input" required>
                @error('password')<span class="form__error">{{ $message }}</span>@enderror
            </label>
            <label class="form__field">
                <span>Confirmar contraseña</span>
                <input type="password" name="password_confirmation" class="input" required>
            </label>
        </div>
        <div class="form__field">
            <span>Roles</span>
            <div class="chip-group">
                @foreach($roles as $role)
                    <label class="chip">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"> {{ $role->name }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form__actions">
            <a class="btn btn--ghost" href="{{ route('users.index') }}">Cancelar</a>
            <button class="btn btn--primary" type="submit">Guardar</button>
        </div>
    </form>
</div>
@endsection
