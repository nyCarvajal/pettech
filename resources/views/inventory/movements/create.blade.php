@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Movimientos manuales</h1></div>
<div class="card">
<form method="POST" action="{{ route('stock.movements.store') }}">@csrf
<label class="form__field"><span>Producto</span><select class="input" name="product_id" required>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select></label>
<label class="form__field"><span>Bodega</span><select class="input" name="warehouse_id" required>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></label>
<label class="form__field"><span>Tipo</span><select class="input" name="movement_type"><option value="in">Entrada</option><option value="out">Salida</option><option value="adjustment">Ajuste</option></select></label>
<label class="form__field"><span>Cantidad</span><input class="input" type="number" step="0.01" name="qty" required></label>
<label class="form__field"><span>Motivo</span><input class="input" name="reason"></label>
<label><input type="checkbox" name="authorized_adjustment" value="1"> Ajuste autorizado por Admin</label>
<button class="btn btn--primary">Registrar</button></form></div>
@endsection
