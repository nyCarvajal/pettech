@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Facturación electrónica</p>
        <h1>Estados DIAN</h1>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Estado DIAN</th>
                    <th>CUFE</th>
                    <th>Enviado</th>
                    <th>Aceptado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($electronicInvoices as $electronicInvoice)
                    <tr>
                        <td>#{{ $electronicInvoice->invoice?->number ?? $electronicInvoice->invoice_id }}</td>
                        <td><span class="badge">{{ $electronicInvoice->dian_status }}</span></td>
                        <td>{{ $electronicInvoice->cufe ?? '—' }}</td>
                        <td>{{ optional($electronicInvoice->sent_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ optional($electronicInvoice->accepted_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="table__actions">
                            <a class="btn btn--ghost" href="{{ route('dian.invoices.show', $electronicInvoice->invoice_id) }}">Ver</a>
                            @if(in_array($electronicInvoice->dian_status, ['error', 'rejected', 'pending'], true))
                                <form method="POST" action="{{ route('dian.invoices.retry', $electronicInvoice->invoice_id) }}">
                                    @csrf
                                    <button class="btn btn--ghost" type="submit">Reintentar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No hay facturas electrónicas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $electronicInvoices->links() }}</div>
</div>
@endsection
