@extends('layouts.app')
@section('content')
<h1 class="mb-4 text-xl font-semibold">Editar tutor</h1>
<form method="POST" action="{{ route('customers.update', $customer) }}" class="rounded border bg-white p-4">
    @method('PUT')
    @include('customers._form', ['customer' => $customer])
</form>
@endsection
