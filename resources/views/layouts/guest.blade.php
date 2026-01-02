<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PetTech') }}</title>
    @vite(['resources/js/app.js', 'resources/scss/style.scss'])
</head>
<body class="dark-theme auth-page">
    <div class="auth-bg">
        <div class="auth-bg__wave auth-bg__wave--one"></div>
        <div class="auth-bg__wave auth-bg__wave--two"></div>
        <div class="auth-bg__gradient"></div>
    </div>

    <div class="auth-shell">
        <div class="auth-brand">
            <img src="{{ asset('images/logo-dark.svg') }}" alt="PetTech" class="auth-brand__logo">
            <div class="auth-brand__text">
                <h1>PetTech</h1>
                <p>Pet Grooming Solutions</p>
            </div>
        </div>
        <div class="auth-card">
            @if(session('status'))
                <div class="alert alert--success">{{ session('status') }}</div>
            @endif
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>
</body>
</html>
