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

    $path = $chartPoints->values()->map(function ($point, $index) use ($max, $height, $padding, $stepX) {
        $x = $padding + ($stepX * $index);
        $y = $height - $padding - (($point['total'] / $max) * ($height - ($padding * 2)));
        return round($x, 2).','.round($y, 2);
    })->implode(' ');

    $periodos = [
        'today' => 'Hoy',
        'week' => 'Semana',
        'month' => 'Mes',
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl border border-sky-200 bg-gradient-to-r from-sky-50 via-indigo-50 to-fuchsia-50 p-5 shadow-sm md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-sky-700">Panel Administrativo</p>
            <h1 class="text-2xl font-bold text-slate-900">Métricas operativas</h1>
            <p class="mt-1 text-sm text-slate-600">Seguimiento de ventas, citas e inventario en tiempo real.</p>
        </div>

        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <label class="text-sm font-medium text-slate-700">Desde
                <input type="date" name="from" value="{{ $range['from'] }}" class="mt-1 w-full rounded-lg border-sky-200 bg-white text-sm focus:border-sky-400 focus:ring-sky-400">
            </label>
            <label class="text-sm font-medium text-slate-700">Hasta
                <input type="date" name="to" value="{{ $range['to'] }}" class="mt-1 w-full rounded-lg border-sky-200 bg-white text-sm focus:border-sky-400 focus:ring-sky-400">
            </label>
            <button class="h-10 self-end rounded-lg bg-gradient-to-r from-sky-600 to-indigo-600 px-4 text-sm font-semibold text-white shadow">Aplicar filtros</button>
        </form>
    </div>

    <section>
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Ventas: hoy, semana y mes</h2>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($salesSummary as $label => $metric)
                <x-dashboard.card :title="$periodos[$label] ?? ucfirst($label)" class="border-sky-100 bg-gradient-to-b from-white to-sky-50/60">
                    <div class="mt-3 space-y-1 text-sm text-slate-700">
                        <p><span class="font-semibold text-sky-700">Total:</span> ${{ number_format($metric['total'], 0, ',', '.') }}</p>
                        <p><span class="font-semibold text-indigo-700">Facturas:</span> {{ $metric['invoices'] }}</p>
                        <p><span class="font-semibold text-fuchsia-700">Ticket promedio:</span> ${{ number_format($metric['avg_ticket'], 0, ',', '.') }}</p>
                    </div>
                </x-dashboard.card>
            @endforeach
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-dashboard.card title="Top 10 productos/servicios por ventas" class="border-emerald-100 bg-gradient-to-b from-white to-emerald-50/40">
            <div class="mt-3 overflow-auto rounded-lg border border-emerald-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-emerald-100/70 text-left text-emerald-800">
                        <tr>
                            <th class="px-3 py-2">Ítem</th>
                            <th class="px-3 py-2">Tipo</th>
                            <th class="px-3 py-2 text-right">Cantidad</th>
                            <th class="px-3 py-2 text-right">Ventas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100 bg-white">
                        @forelse($topItems as $item)
                            <tr class="hover:bg-emerald-50/60">
                                <td class="px-3 py-2">{{ $item->item_name }}</td>
                                <td class="px-3 py-2">{{ $item->item_type === 'service' ? 'Servicio' : 'Producto' }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($item->qty_sold, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-emerald-700">${{ number_format($item->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">Sin datos para el rango seleccionado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>

        <x-dashboard.card title="Citas del día por estado" :subtitle="$range['to']" class="border-violet-100 bg-gradient-to-b from-white to-violet-50/40">
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($appointmentsByStatus as $row)
                    <li class="flex items-center justify-between rounded-lg border border-violet-100 bg-violet-50 px-3 py-2">
                        <span class="capitalize text-slate-700">{{ str_replace('_', ' ', $row->status) }}</span>
                        <span class="rounded-full bg-violet-600 px-2 py-0.5 text-xs font-semibold text-white">{{ $row->total }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">No hay citas para la fecha seleccionada.</li>
                @endforelse
            </ul>
        </x-dashboard.card>
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-dashboard.card title="Alertas de stock bajo" :value="$alerts['low_stock_count']" class="border-amber-100 bg-gradient-to-b from-white to-amber-50/60">
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($alerts['low_stock_items'] as $stockItem)
                    <li class="flex items-center justify-between rounded-lg border border-amber-100 bg-amber-50 px-3 py-2">
                        <span>{{ $stockItem->name }}</span>
                        <span class="font-medium text-amber-800">{{ number_format($stockItem->stock_total, 0, ',', '.') }} / mínimo {{ number_format($stockItem->min_stock, 0, ',', '.') }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">Sin productos en nivel crítico.</li>
                @endforelse
            </ul>
        </x-dashboard.card>

        <x-dashboard.card title="Facturas DIAN rechazadas/error" :value="$alerts['dian_error_count']" class="border-rose-100 bg-gradient-to-b from-white to-rose-50/60">
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($alerts['dian_error_items'] as $errorItem)
                    <li class="flex items-center justify-between rounded-lg border border-rose-100 bg-rose-50 px-3 py-2">
                        <span>Factura #{{ $errorItem['invoice_id'] }} ({{ $errorItem['source'] === 'dian_documents' ? 'DIAN' : 'Factura electrónica' }})</span>
                        <span class="rounded-full bg-rose-600 px-2 py-0.5 text-xs font-semibold uppercase text-white">{{ $errorItem['status'] }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">Sin errores recientes de DIAN.</li>
                @endforelse
            </ul>
        </x-dashboard.card>
    </section>

    <x-dashboard.card title="Ventas de los últimos 14 días" class="border-cyan-100 bg-gradient-to-b from-white to-cyan-50/40">
        <div class="mt-3 overflow-x-auto">
            <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-56 min-w-[680px] w-full rounded-xl bg-white">
                <defs>
                    <linearGradient id="lineaVentas" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#06b6d4" />
                        <stop offset="100%" stop-color="#4f46e5" />
                    </linearGradient>
                </defs>
                <line x1="{{ $padding }}" y1="{{ $height - $padding }}" x2="{{ $width - $padding }}" y2="{{ $height - $padding }}" stroke="#bae6fd" stroke-width="1" />
                <polyline fill="none" stroke="url(#lineaVentas)" stroke-width="3.5" points="{{ $path }}" />
                @foreach($chartPoints as $index => $point)
                    @php
                        $x = $padding + ($stepX * $index);
                        $y = $height - $padding - (($point['total'] / $max) * ($height - ($padding * 2)));
                    @endphp
                    <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#2563eb" />
                @endforeach
            </svg>
            <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-slate-500 sm:grid-cols-4 md:grid-cols-7">
                @foreach($chartPoints as $point)
                    <div class="rounded-lg bg-cyan-50 px-2 py-1">
                        <p>{{ \Carbon\Carbon::parse($point['date'])->format('d/m') }}</p>
                        <p class="font-semibold text-cyan-800">${{ number_format($point['total'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-dashboard.card>
</div>
@endsection
