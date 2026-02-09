@extends('layouts.app')

@section('content')
<div class="page-header"><div><h1>Categorías</h1></div><a class="btn btn--primary" href="{{ route('categories.create') }}">Nueva</a></div>
<div class="card"><table class="table"><thead><tr><th>Nombre</th><th></th></tr></thead><tbody>@forelse($categories as $category)<tr><td>{{ $category->name }}</td><td class="table__actions"><a class="btn btn--ghost" href="{{ route('categories.edit', $category) }}">Editar</a><form method="POST" action="{{ route('categories.destroy', $category) }}">@csrf @method('DELETE')<button class="btn btn--ghost">Eliminar</button></form></td></tr>@empty<tr><td colspan="2">Sin categorías.</td></tr>@endforelse</tbody></table><div class="pagination">{{ $categories->links() }}</div></div>
@endsection
