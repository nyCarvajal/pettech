@extends('layouts.app')
@section('content')<div class="card"><h1>Nuevo producto</h1><form method="POST" action="{{ route('products.store') }}">@csrf @include('inventory.products.form')<button class="btn btn--primary">Guardar</button></form></div>@endsection
