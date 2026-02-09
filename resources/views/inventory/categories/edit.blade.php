@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Editar categoría</h1></div>
<div class="card"><form method="POST" action="{{ route('categories.update', $category) }}">@csrf @method('PUT') @include('inventory.categories.form')<button class="btn btn--primary">Actualizar</button></form></div>
@endsection
