<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $invoice->number ?? $invoice->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; }
        .totals { margin-top: 16px; }
        .totals div { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>Factura POS</h2>
            <p>No. {{ $invoice->number ?? 'Borrador' }}</p>
            <p>Fecha: {{ $invoice->issued_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <p>Cliente: {{ $invoice->customer?->name ?? 'Venta mostrador' }}</p>
            <p>Mascota: {{ $invoice->pet?->name ?? 'N/A' }}</p>
        </div>
    </div>

    <table>
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
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->qty ?? $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->tax_rate, 2) }}%</td>
                    <td>${{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><strong>${{ number_format($invoice->subtotal, 2) }}</strong></div>
        <div><span>Impuestos</span><strong>${{ number_format($invoice->tax_total, 2) }}</strong></div>
        <div><span>Total</span><strong>${{ number_format($invoice->total, 2) }}</strong></div>
    </div>

    <h4>Pagos</h4>
    <ul>
        @foreach($invoice->posPayments as $payment)
            <li>{{ strtoupper($payment->method) }} - ${{ number_format($payment->amount, 2) }}</li>
        @endforeach
    </ul>
</body>
</html>
