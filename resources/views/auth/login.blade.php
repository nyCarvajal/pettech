@extends('layouts.guest')

@section('content')
<div class="auth-card__header">
    <div>
        <h2>Bienvenido de nuevo</h2>
        <p class="text-muted">Ingresa tus credenciales para acceder</p>
    </div>
</div>
<form class="form" method="POST" action="{{ route('login') }}">
    @csrf
    <label class="form__field">
        <span>Email</span>
        <input type="email" name="email" class="input" value="{{ old('email') }}" required autofocus>
        @error('email')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Contraseña</span>
        <input type="password" name="password" class="input" required>
        @error('password')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__checkbox">
        <input type="checkbox" name="remember"> Mantener sesión
    </label>
    <button class="btn btn--primary w-full" type="submit">Entrar</button>
</form>
@endsection
