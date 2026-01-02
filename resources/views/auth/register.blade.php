@extends('layouts.guest')

@section('content')
<div class="auth-card__header">
    <div>
        <h2>Crear cuenta</h2>
        <p class="text-muted">Regístrate para empezar</p>
    </div>
</div>
<form class="form" method="POST" action="{{ route('register') }}">
    @csrf
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
    <label class="form__field">
        <span>Contraseña</span>
        <input type="password" name="password" class="input" required>
        @error('password')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Confirmar contraseña</span>
        <input type="password" name="password_confirmation" class="input" required>
    </label>
    <button class="btn btn--primary w-full" type="submit">Registrarme</button>
</form>
@endsection
