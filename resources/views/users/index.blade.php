@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Seguridad</p>
        <h1>Usuarios</h1>
    </div>
    <a class="btn btn--primary" href="{{ route('users.create') }}">Nuevo usuario</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <form action="{{ route('users.toggle', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="badge badge--ghost" type="submit">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</button>
                            </form>
                        </td>
                        <td class="table__actions">
                            <a class="btn btn--ghost" href="{{ route('users.edit', $user) }}">Editar</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn--ghost" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection
