<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PetTech') }}</title>
    @vite(['resources/js/app.js', 'resources/scss/style.scss'])
</head>
<body class="dark-theme">
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar__brand">PetTech</div>
            <nav class="sidebar__nav">
                <a href="{{ route('dashboard') }}" class="sidebar__link">Dashboard</a>
                <a href="{{ route('users.index') }}" class="sidebar__link">Usuarios</a>
                <a href="{{ route('roles.index') }}" class="sidebar__link">Roles</a>
                <a href="{{ route('permissions.index') }}" class="sidebar__link">Permisos</a>
                <a href="{{ route('clients.index') }}" class="sidebar__link">Clientes</a>
                <a href="{{ route('appointments.index') }}" class="sidebar__link">Agenda</a>
            </nav>
        </aside>
        <div class="app-shell__content">
            <header class="topbar">
                <div class="topbar__title">{{ $title ?? 'Panel' }}</div>
                <div class="topbar__actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn--ghost">Cerrar sesión</button>
                    </form>
                </div>
            </header>
            <main class="content">
                @if(session('status'))
                    <div class="alert alert--success">{{ session('status') }}</div>
                @endif
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
