@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Bodegas</h1><a class="btn btn--primary" href="{{ route('warehouses.create') }}">Nueva</a></div>
<div class="card"><table class="table"><thead><tr><th>Nombre</th><th>Código</th><th>Ubicación</th><th></th></tr></thead><tbody>@foreach($warehouses as $warehouse)<tr><td>{{ $warehouse->name }}</td><td>{{ $warehouse->code }}</td><td>{{ $warehouse->location }}</td><td class="table__actions"><a class="btn btn--ghost" href="{{ route('warehouses.edit', $warehouse) }}">Editar</a><form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}">@csrf @method('DELETE')<button class="btn btn--ghost">Eliminar</button></form></td></tr>@endforeach</tbody></table><div class="pagination">{{ $warehouses->links() }}</div></div>
@endsection
