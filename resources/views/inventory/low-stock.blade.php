@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Productos por reponer</h1></div>
<div class="card"><table class="table"><thead><tr><th>SKU</th><th>Producto</th><th>Stock actual</th><th>Stock mínimo</th></tr></thead><tbody>@forelse($products as $product)<tr><td>{{ $product->sku }}</td><td>{{ $product->name }}</td><td>{{ $product->stocks->sum('qty') }}</td><td>{{ $product->min_stock }}</td></tr>@empty<tr><td colspan="4">No hay alertas de reposición.</td></tr>@endforelse</tbody></table></div>
@endsection
