@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card__header">
        <h2>Iniciar sesión</h2>
        <p class="text-muted">Accede al panel seguro</p>
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
</div>
@endsection
