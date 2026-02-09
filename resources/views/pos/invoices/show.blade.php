@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card__header">
            <div>
                <h2 class="card__title">Factura POS #{{ $invoice->number ?? 'Borrador' }}</h2>
                <p class="text-muted">Estado: {{ strtoupper($invoice->status) }}</p>
            </div>
            <div class="actions">
                <a class="btn btn--ghost" href="{{ route('pos.invoices.print', $invoice) }}" target="_blank">Imprimir</a>
                <a class="btn btn--ghost" href="{{ route('pos.invoices.pdf', $invoice) }}" target="_blank">PDF</a>
            </div>
        </div>

        <div class="grid grid--2">
            <div>
                <h3>Buscador de productos</h3>
                <form method="GET" action="{{ route('pos.invoices.show', $invoice) }}" class="inline-form">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar producto..." />
                    <button class="btn btn--ghost" type="submit">Buscar</button>
                </form>

                <div class="product-list">
                    @forelse($products as $product)
                        <form method="POST" action="{{ route('pos.invoices.items.store', $invoice) }}" class="product-card">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="product-info">
                                <strong>{{ $product->name }}</strong>
                                <span>${{ number_format($product->sale_price, 2) }}</span>
                            </div>
                            <div class="product-actions">
                                <input type="number" name="qty" value="1" min="0.01" step="0.01">
                                <button class="btn btn--primary" type="submit">Agregar</button>
                            </div>
                        </form>
                    @empty
                        <p class="text-muted">No hay productos para mostrar.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3>Agregar servicio manual</h3>
                <form method="POST" action="{{ route('pos.invoices.items.store', $invoice) }}" class="form-grid">
                    @csrf
                    <label class="form-field">
                        <span>Descripción</span>
                        <input type="text" name="description" required>
                    </label>
                    <label class="form-field">
                        <span>Cantidad</span>
                        <input type="number" name="qty" value="1" min="0.01" step="0.01">
                    </label>
                    <label class="form-field">
                        <span>Precio unitario</span>
                        <input type="number" name="unit_price" value="0" min="0" step="0.01">
                    </label>
                    <label class="form-field">
                        <span>Impuesto %</span>
                        <input type="number" name="tax_rate" value="0" min="0" step="0.01">
                    </label>
                    <input type="hidden" name="is_service" value="1">
                    <button class="btn btn--primary" type="submit">Agregar servicio</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Items</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->qty ?? $item->quantity }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->tax_rate, 2) }}%</td>
                        <td>${{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">Agrega productos o servicios.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid--2">
        <div class="card">
            <h3>Totalizador</h3>
            <ul class="summary-list">
                <li><span>Subtotal</span><strong>${{ number_format($invoice->subtotal, 2) }}</strong></li>
                <li><span>Impuestos</span><strong>${{ number_format($invoice->tax_total, 2) }}</strong></li>
                <li><span>Total</span><strong>${{ number_format($invoice->total, 2) }}</strong></li>
            </ul>
        </div>

        <div class="card">
            <h3>Pagos</h3>
            <form method="POST" action="{{ route('pos.invoices.payments.store', $invoice) }}" class="form-grid">
                @csrf
                <label class="form-field">
                    <span>Método</span>
                    <select name="method">
                        <option value="cash">Efectivo</option>
                        <option value="card">Tarjeta</option>
                        <option value="transfer">Transferencia</option>
                    </select>
                </label>
                <label class="form-field">
                    <span>Monto</span>
                    <input type="number" name="amount" min="0.01" step="0.01" required>
                </label>
                <label class="form-field">
                    <span>Fecha</span>
                    <input type="datetime-local" name="paid_at" value="{{ now()->format('Y-m-d\\TH:i') }}" required>
                </label>
                <label class="form-field">
                    <span>Referencia</span>
                    <input type="text" name="reference">
                </label>
                <button class="btn btn--primary" type="submit">Registrar pago</button>
            </form>

            <ul class="summary-list">
                @foreach($invoice->posPayments as $payment)
                    <li>
                        <span>{{ strtoupper($payment->method) }} - {{ $payment->paid_at?->format('d/m/Y H:i') }}</span>
                        <strong>${{ number_format($payment->amount, 2) }}</strong>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <form method="POST" action="{{ route('pos.invoices.issue', $invoice) }}" class="actions">
        @csrf
        <button class="btn btn--primary" type="submit">Emitir factura</button>
    </form>
@endsection
