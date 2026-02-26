@extends('layouts.app')
@section('content')
<h1 class="mb-4 text-xl font-semibold">Nuevo tutor</h1>
<form method="POST" action="{{ route('customers.store') }}" class="rounded border bg-white p-4">
    @include('customers._form')
</form>
@endsection
