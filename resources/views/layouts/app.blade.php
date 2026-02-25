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
                <p class="eyebrow">Menú principal</p>
                <a href="{{ route('dashboard') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🏠</span>Dashboard</a>
                <a href="{{ route('users.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">👤</span>Usuarios</a>
                <a href="{{ route('roles.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🧩</span>Roles</a>
                <a href="{{ route('permissions.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🔐</span>Permisos</a>
                <a href="{{ route('clients.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🐾</span>Clientes</a>
                <a href="{{ route('appointments.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🗓️</span>Agenda</a>
                <a href="{{ route('pos.invoices.create') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">💳</span>POS</a>
                <a href="{{ route('dian.invoices.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🧾</span>DIAN estados</a>
                <a href="{{ route('dian.config.edit') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">⚙️</span>DIAN configuración</a>

                <p class="eyebrow" style="margin-top:12px;">Inventario</p>
                <a href="{{ route('products.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">📦</span>Productos</a>
                <a href="{{ route('categories.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🗂️</span>Categorías</a>
                <a href="{{ route('warehouses.index') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🏬</span>Bodegas</a>
                <a href="{{ route('stock.movements.create') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">🔄</span>Movimientos</a>
                <a href="{{ route('stock.low') }}" class="sidebar__link"><span class="sidebar__icon" aria-hidden="true">⚠️</span>Por reponer</a>
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
