@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="card__title">Nueva factura POS</h2>
        <form method="POST" action="{{ route('pos.invoices.store') }}" class="form-grid">
            @csrf
            <label class="form-field">
                <span>Cliente (opcional)</span>
                <select name="customer_id">
                    <option value="">Venta mostrador</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="form-field">
                <span>Mascota (opcional)</span>
                <select name="pet_id">
                    <option value="">Sin mascota</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="form-field">
                <span>Notas</span>
                <textarea name="notes" rows="3"></textarea>
            </label>

            <button class="btn btn--primary" type="submit">Crear factura</button>
        </form>
    </div>
@endsection
