@extends('layouts.app')
@section('content')<div class="card"><h1>Editar bodega</h1><form method="POST" action="{{ route('warehouses.update', $warehouse) }}">@csrf @method('PUT') @include('inventory.warehouses.form')<button class="btn btn--primary">Actualizar</button></form></div>@endsection
