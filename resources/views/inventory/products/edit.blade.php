@extends('layouts.app')
@section('content')<div class="card"><h1>Editar producto</h1><form method="POST" action="{{ route('products.update', $product) }}">@csrf @method('PUT') @include('inventory.products.form')<button class="btn btn--primary">Actualizar</button></form></div>@endsection
