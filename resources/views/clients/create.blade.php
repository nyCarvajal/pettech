@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">CRM</p>
        <h1>Nuevo cliente</h1>
    </div>
</div>
<div class="card">
    <form class="form" method="POST" action="{{ route('clients.store') }}">
        @include('clients._form')
    </form>
</div>
@endsection
