@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Kardex - {{ $product->name }}</h1></div>
<div class="card"><table class="table"><thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Bodega</th><th>Motivo</th><th>Saldo</th></tr></thead><tbody>@foreach($kardex as $movement)<tr><td>{{ $movement->created_at }}</td><td>{{ $movement->movement_type }}</td><td>{{ $movement->qty }}</td><td>{{ $movement->warehouse?->name }}</td><td>{{ $movement->reason }}</td><td>{{ $movement->balance }}</td></tr>@endforeach</tbody></table></div>
@endsection
