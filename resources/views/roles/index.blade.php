@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Seguridad</p>
        <h1>Roles</h1>
    </div>
    <a class="btn btn--primary" href="{{ route('roles.create') }}">Nuevo rol</a>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Permisos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        @foreach($role->permissions as $permission)
                            <span class="badge">{{ $permission->name }}</span>
                        @endforeach
                    </td>
                    <td class="table__actions">
                        <a class="btn btn--ghost" href="{{ route('roles.edit', $role) }}">Editar</a>
                        <form action="{{ route('roles.destroy', $role) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn--ghost" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">{{ $roles->links() }}</div>
</div>
@endsection
