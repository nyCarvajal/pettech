@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
@php
    $chartPoints = collect($salesChart);
    $max = max(1, (float) $chartPoints->max('total'));
    $width = 700;
    $height = 220;
    $padding = 24;
    $stepX = max(1, ($width - ($padding * 2)) / max(1, $chartPoints->count() - 1));

    $path = $chartPoints->values()->map(function ($point, $index) use ($max, $width, $height, $padding, $stepX) {
        $x = $padding + ($stepX * $index);
        $y = $height - $padding - (($point['total'] / $max) * ($height - ($padding * 2)));
        return round($x, 2).','.round($y, 2);
    })->implode(' ');
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Dashboard Admin</p>
            <h1 class="text-2xl font-bold text-slate-900">Métricas operativas</h1>
        </div>

        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <label class="text-sm text-slate-600">Desde
                <input type="date" name="from" value="{{ $range['from'] }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            </label>
            <label class="text-sm text-slate-600">Hasta
                <input type="date" name="to" value="{{ $range['to'] }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            </label>
            <button class="h-10 self-end rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white">Aplicar filtros</button>
        </form>
    </div>

    <section>
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Ventas hoy, semana y mes</h2>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($salesSummary as $label => $metric)
                <x-dashboard.card :title="ucfirst($label)">
                    <div class="mt-3 space-y-1 text-sm text-slate-700">
                        <p><span class="font-medium">Total:</span> ${{ number_format($metric['total'], 0, ',', '.') }}</p>
                        <p><span class="font-medium"># Facturas:</span> {{ $metric['invoices'] }}</p>
                        <p><span class="font-medium">Ticket prom.:</span> ${{ number_format($metric['avg_ticket'], 0, ',', '.') }}</p>
                    </div>
                </x-dashboard.card>
            @endforeach
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-dashboard.card title="Top 10 productos/servicios por ventas">
            <div class="mt-3 overflow-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-500">
                        <tr>
                            <th class="pb-2">Ítem</th>
                            <th class="pb-2">Tipo</th>
                            <th class="pb-2 text-right">Cantidad</th>
                            <th class="pb-2 text-right">Ventas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topItems as $item)
                            <tr>
                                <td class="py-2">{{ $item->item_name }}</td>
                                <td class="py-2">{{ $item->item_type }}</td>
                                <td class="py-2 text-right">{{ number_format($item->qty_sold, 0, ',', '.') }}</td>
                                <td class="py-2 text-right">${{ number_format($item->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-slate-500">Sin datos en el rango seleccionado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>

        <x-dashboard.card title="Citas del día por estado" :subtitle="$range['to']">
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($appointmentsByStatus as $row)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <span class="capitalize text-slate-700">{{ str_replace('_', ' ', $row->status) }}</span>
                        <span class="font-semibold text-slate-900">{{ $row->total }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">No hay citas para la fecha seleccionada.</li>
                @endforelse
            </ul>
        </x-dashboard.card>
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-dashboard.card title="Alertas de stock bajo" :value="$alerts['low_stock_count']">
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($alerts['low_stock_items'] as $stockItem)
                    <li class="flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2">
                        <span>{{ $stockItem->name }}</span>
                        <span class="font-medium">{{ number_format($stockItem->stock_total, 0, ',', '.') }} / min {{ number_format($stockItem->min_stock, 0, ',', '.') }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">Sin productos en nivel crítico.</li>
                @endforelse
            </ul>
        </x-dashboard.card>

        <x-dashboard.card title="Facturas DIAN rechazadas/error" :value="$alerts['dian_error_count']">
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($alerts['dian_error_items'] as $errorItem)
                    <li class="flex items-center justify-between rounded-lg bg-rose-50 px-3 py-2">
                        <span>Factura #{{ $errorItem['invoice_id'] }} ({{ $errorItem['source'] }})</span>
                        <span class="font-medium uppercase">{{ $errorItem['status'] }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">Sin errores recientes de DIAN.</li>
                @endforelse
            </ul>
        </x-dashboard.card>
    </section>

    <x-dashboard.card title="Ventas últimos 14 días">
        <div class="mt-3 overflow-x-auto">
            <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-56 min-w-[680px] w-full">
                <line x1="{{ $padding }}" y1="{{ $height - $padding }}" x2="{{ $width - $padding }}" y2="{{ $height - $padding }}" stroke="#e2e8f0" stroke-width="1" />
                <polyline fill="none" stroke="#0f172a" stroke-width="3" points="{{ $path }}" />
                @foreach($chartPoints as $index => $point)
                    @php
                        $x = $padding + ($stepX * $index);
                        $y = $height - $padding - (($point['total'] / $max) * ($height - ($padding * 2)));
                    @endphp
                    <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#334155" />
                @endforeach
            </svg>
            <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-slate-500 sm:grid-cols-4 md:grid-cols-7">
                @foreach($chartPoints as $point)
                    <div>
                        <p>{{ \Carbon\Carbon::parse($point['date'])->format('d/m') }}</p>
                        <p class="font-medium text-slate-700">${{ number_format($point['total'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-dashboard.card>
</div>
@endsection
