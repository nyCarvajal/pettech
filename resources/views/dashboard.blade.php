<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel oscuro</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo">PT</div>
            <div class="text">
                <span class="title">Pettech</span>
                <span class="subtitle">Tema oscuro</span>
            </div>
        </div>

        <div class="nav-section">
            <h6>Navegación</h6>
            <div class="nav-links">
                <a class="nav-link active" href="#">
                    <span class="dot"></span>
                    <span class="label">Panel</span>
                </a>
                <a class="nav-link" href="#">
                    <span class="dot"></span>
                    <span class="label">Reservas</span>
                </a>
                <a class="nav-link" href="#">
                    <span class="dot"></span>
                    <span class="label">Clientes</span>
                </a>
                <a class="nav-link" href="#">
                    <span class="dot"></span>
                    <span class="label">Reportes</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <h6>Acciones</h6>
            <div class="nav-links">
                <a class="nav-link" href="#">
                    <span class="dot"></span>
                    <span class="label">Nuevo turno</span>
                </a>
                <a class="nav-link" href="#">
                    <span class="dot"></span>
                    <span class="label">Configurar</span>
                </a>
            </div>
        </div>

        <div class="user-card">
            <div class="avatar">LC</div>
            <div class="user-meta">
                <span class="name">Laura Cárdenas</span>
                <span class="role">Administrador</span>
            </div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-left">
                <h1>Resumen</h1>
                <span class="badge">Tema oscuro</span>
            </div>
            <div class="topbar-actions">
                <div class="search">
                    <span class="hint">⌕</span>
                    <input type="text" placeholder="Buscar...">
                </div>
                <button class="btn btn-secondary">Preferencias</button>
                <button class="btn btn-primary" data-modal-trigger>Crear</button>
            </div>
        </header>

        <section class="content">
            <div class="grid">
                <div class="card">
                    <div class="badge">Sesiones activas</div>
                    <div class="metric">128 <small>+12%</small></div>
                    <p>Clientes conectados a tu club en las últimas 24h.</p>
                </div>
                <div class="card">
                    <div class="badge chip-muted">Ingresos</div>
                    <div class="metric">$18,4k <small>mes</small></div>
                    <p>Promedio calculado con base en reservas confirmadas.</p>
                </div>
                <div class="card">
                    <div class="badge">Satisfacción</div>
                    <div class="metric">4.8 <small>/5</small></div>
                    <p>Encuestas respondidas esta semana.</p>
                </div>
            </div>

            <div class="card">
                <h3>Componentes</h3>
                <div class="divider"></div>
                <div class="component-row">
                    <button class="btn btn-primary">Botón primario</button>
                    <button class="btn btn-secondary">Botón secundario</button>
                    <button class="btn btn-ghost">Botón ghost</button>
                    <span class="badge">Badge</span>
                    <span class="badge chip-muted">Estado neutro</span>
                </div>
                <div class="divider"></div>
                <div class="form-row">
                    <label for="input">Input</label>
                    <input id="input" class="form-control" type="text" placeholder="Ingresa algo...">
                </div>
                <div class="form-row">
                    <label for="select">Select</label>
                    <select id="select" class="form-control">
                        <option>Selecciona una opción</option>
                        <option>Opción 1</option>
                        <option>Opción 2</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <h3>Modal</h3>
                <p>Ejemplo de modal con fondo oscuro y botones contrastados.</p>
                <div class="component-row">
                    <button class="btn btn-primary" data-modal-trigger>Mostrar modal</button>
                </div>
            </div>
        </section>
    </main>
</div>

<div class="modal-backdrop" data-modal>
    <div class="modal">
        <header>
            <h3>Crear elemento</h3>
            <button class="btn btn-ghost" data-modal-close>✕</button>
        </header>
        <p class="text-muted">Usa este modal para flujos rápidos. Mantiene bordes sutiles y sombras suaves.</p>
        <footer>
            <button class="btn btn-secondary" data-modal-close>Cancelar</button>
            <button class="btn btn-primary">Guardar</button>
        </footer>
    </div>
</div>
</body>
</html>
