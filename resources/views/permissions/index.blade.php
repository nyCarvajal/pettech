@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Seguridad</p>
        <h1>Permisos</h1>
    </div>
    <a class="btn btn--primary" href="{{ route('permissions.create') }}">Nuevo permiso</a>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->description }}</td>
                    <td class="table__actions">
                        <a class="btn btn--ghost" href="{{ route('permissions.edit', $permission) }}">Editar</a>
                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn--ghost" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">{{ $permissions->links() }}</div>
</div>
@endsection
