@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Productos</h1><a class="btn btn--primary" href="{{ route('products.create') }}">Nuevo</a></div>
<div class="card"><table class="table"><thead><tr><th>SKU</th><th>Nombre</th><th>Categoría</th><th>Tipo</th><th></th></tr></thead><tbody>@foreach($products as $product)<tr><td>{{ $product->sku }}</td><td>{{ $product->name }}</td><td>{{ $product->category?->name ?? '—' }}</td><td>{{ $product->is_service ? 'Servicio' : 'Producto' }}</td><td class="table__actions"><a class="btn btn--ghost" href="{{ route('products.kardex', $product) }}">Kardex</a><a class="btn btn--ghost" href="{{ route('products.edit', $product) }}">Editar</a></td></tr>@endforeach</tbody></table><div class="pagination">{{ $products->links() }}</div></div>
@endsection
