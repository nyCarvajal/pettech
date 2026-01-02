@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">CRM</p>
        <h1>Clientes</h1>
        <p class="muted">Gestiona clientes con búsqueda y filtros rápidos.</p>
    </div>
    <a class="btn btn--primary" href="{{ route('clients.create') }}">Nuevo cliente</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('clients.index') }}" class="filters">
        <div class="filters__controls">
            <label class="form__field">
                <span>Buscar</span>
                <input type="search" name="search" class="input" placeholder="Nombre, correo, teléfono" value="{{ $search }}">
            </label>
            <label class="form__field">
                <span>Estado</span>
                <select class="input" name="status">
                    <option value="">Todos</option>
                    <option value="active" @selected($status === 'active')>Activos</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactivos</option>
                </select>
            </label>
        </div>
        <div class="filters__actions">
            <button class="btn btn--secondary" type="submit">Aplicar</button>
            <a class="btn btn--ghost" href="{{ route('clients.index') }}">Limpiar</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Contacto</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>
                            <div class="stack">
                                <strong>{{ $client->name }}</strong>
                                @if($client->address)
                                    <span class="muted">{{ $client->address }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="stack">
                                @if($client->email)
                                    <span>{{ $client->email }}</span>
                                @endif
                                @if($client->phone)
                                    <span class="muted">{{ $client->phone }}</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $client->document ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $client->active ? '' : 'badge--ghost' }}">{{ $client->active ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="table__actions">
                            <a class="btn btn--ghost" href="{{ route('clients.show', $client) }}">Detalle</a>
                            <a class="btn btn--ghost" href="{{ route('clients.edit', $client) }}">Editar</a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn--ghost" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Sin clientes por mostrar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $clients->links() }}</div>
</div>
@endsection
