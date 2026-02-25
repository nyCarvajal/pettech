@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Factura #{{ $invoice->number ?? $invoice->id }}</p>
        <h1>Detalle DIAN</h1>
    </div>
    <form method="POST" action="{{ route('dian.invoices.retry', $invoice) }}">
        @csrf
        <button class="btn btn--primary" type="submit">Reintentar envío</button>
    </form>
</div>

<div class="card" style="padding: 20px;">
    <p><strong>Estado:</strong> {{ $electronicInvoice->dian_status }}</p>
    <p><strong>CUFE:</strong> {{ $electronicInvoice->cufe ?? '—' }}</p>
    <p><strong>XML:</strong> {{ $electronicInvoice->xml_path ?? '—' }}</p>
    <p><strong>Último error:</strong> {{ $electronicInvoice->last_error ?? '—' }}</p>
    <p><strong>Respuesta proveedor:</strong></p>
    <pre>{{ json_encode($electronicInvoice->response_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
</div>
@endsection
