@extends('layouts.app')
@section('content')
<div class="page-header"><h1>Nueva categoría</h1></div>
<div class="card"><form method="POST" action="{{ route('categories.store') }}">@csrf @include('inventory.categories.form')<button class="btn btn--primary">Guardar</button></form></div>
@endsection
