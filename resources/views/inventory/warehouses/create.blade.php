@extends('layouts.app')
@section('content')<div class="card"><h1>Nueva bodega</h1><form method="POST" action="{{ route('warehouses.store') }}">@csrf @include('inventory.warehouses.form')<button class="btn btn--primary">Guardar</button></form></div>@endsection
