<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DianDocument;
use App\Models\ElectronicInvoice;
use App\Models\Invoice;
use App\Models\InventoryStock;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(?string $fromDate = null, ?string $toDate = null): array
    {
        [$from, $to] = $this->resolveDateRange($fromDate, $toDate);

        return [
            'range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'salesSummary' => $this->getSalesSummaryByPeriods(),
            'topItems' => $this->getTopProductsAndServices($from, $to),
            'appointmentsByStatus' => $this->getAppointmentsByStatusForDay($to),
            'alerts' => $this->getAlerts(),
            'salesChart' => $this->getSalesLast14Days($to),
        ];
    }

    private function resolveDateRange(?string $fromDate, ?string $toDate): array
    {
        $from = $fromDate ? Carbon::parse($fromDate)->startOfDay() : now()->startOfMonth();
        $to = $toDate ? Carbon::parse($toDate)->endOfDay() : now()->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    private function getSalesSummaryByPeriods(): array
    {
        $now = now();

        return [
            'today' => $this->aggregateSales($now->copy()->startOfDay(), $now->copy()->endOfDay()),
            'week' => $this->aggregateSales($now->copy()->startOfWeek(), $now->copy()->endOfWeek()),
            'month' => $this->aggregateSales($now->copy()->startOfMonth(), $now->copy()->endOfMonth()),
        ];
    }

    private function aggregateSales(Carbon $from, Carbon $to): array
    {
        $query = Invoice::query()
            ->whereBetween('issued_at', [$from, $to])
            ->whereNotNull('issued_at')
            ->whereNotIn('status', ['draft', 'void', 'cancelled']);

        if ($tenantId = $this->tenantId()) {
            $query->where('tenant_id', $tenantId);
        }

        $row = $query
            ->selectRaw('COUNT(*) as invoices_count')
            ->selectRaw('COALESCE(SUM(COALESCE(total, grand_total)), 0) as total_sales')
            ->first();

        $total = (float) ($row->total_sales ?? 0);
        $count = (int) ($row->invoices_count ?? 0);

        return [
            'total' => $total,
            'invoices' => $count,
            'avg_ticket' => $count > 0 ? round($total / $count, 2) : 0,
        ];
    }

    private function getTopProductsAndServices(Carbon $from, Carbon $to)
    {
        $query = DB::table('invoice_items as ii')
            ->join('invoices as i', 'i.id', '=', 'ii.invoice_id')
            ->leftJoin('products as p', 'p.id', '=', 'ii.product_id')
            ->leftJoin('service_catalog as s', 's.id', '=', 'ii.service_id')
            ->whereBetween('i.issued_at', [$from, $to])
            ->whereNotNull('i.issued_at')
            ->whereNotIn('i.status', ['draft', 'void', 'cancelled'])
            ->whereNull('ii.deleted_at')
            ->whereNull('i.deleted_at');

        if ($tenantId = $this->tenantId()) {
            $query->where('i.tenant_id', $tenantId)
                ->where('ii.tenant_id', $tenantId);
        }

        return $query
            ->selectRaw("COALESCE(ii.item_type, CASE WHEN ii.is_service = 1 THEN 'service' ELSE 'product' END) as item_type")
            ->selectRaw('COALESCE(ii.product_id, ii.service_id, 0) as item_id')
            ->selectRaw("COALESCE(p.name, s.name, ii.description, 'Sin nombre') as item_name")
            ->selectRaw('SUM(ii.quantity) as qty_sold')
            ->selectRaw('SUM(ii.line_total) as total_sales')
            ->groupBy('item_type', 'item_id', 'item_name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();
    }

    private function getAppointmentsByStatusForDay(Carbon $date)
    {
        $query = Appointment::query();

        if ($tenantId = $this->tenantId()) {
            $query->where('tenant_id', $tenantId);
        }

        return $query
            ->whereDate(DB::raw('COALESCE(start_at, scheduled_start)'), $date->toDateString())
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();
    }

    private function getAlerts(): array
    {
        $lowStockQuery = InventoryStock::query()
            ->join('products', 'products.id', '=', 'inventory_stocks.product_id')
            ->whereNull('inventory_stocks.deleted_at')
            ->whereNull('products.deleted_at')
            ->where('products.is_service', false)
            ->groupBy('inventory_stocks.product_id', 'products.name', 'products.min_stock')
            ->havingRaw('SUM(inventory_stocks.stock) <= products.min_stock');

        if ($tenantId = $this->tenantId()) {
            $lowStockQuery->where('inventory_stocks.tenant_id', $tenantId)
                ->where('products.tenant_id', $tenantId);
        }

        $lowStock = $lowStockQuery
            ->selectRaw('inventory_stocks.product_id')
            ->selectRaw('products.name')
            ->selectRaw('products.min_stock')
            ->selectRaw('SUM(inventory_stocks.stock) as stock_total')
            ->orderBy('stock_total')
            ->limit(10)
            ->get();

        $dianStatuses = ['rejected', 'error', 'failed'];

        $dianDocuments = DianDocument::query()
            ->when($this->tenantId(), fn ($q, $tenantId) => $q->where('tenant_id', $tenantId))
            ->whereIn('dian_status', $dianStatuses)
            ->latest('updated_at')
            ->limit(10)
            ->get(['invoice_id', 'dian_status', 'updated_at']);

        $electronicInvoices = ElectronicInvoice::query()
            ->when($this->tenantId(), fn ($q, $tenantId) => $q->where('tenant_id', $tenantId))
            ->whereIn('dian_status', $dianStatuses)
            ->latest('updated_at')
            ->limit(10)
            ->get(['invoice_id', 'dian_status', 'updated_at']);

        return [
            'low_stock_count' => $lowStock->count(),
            'low_stock_items' => $lowStock,
            'dian_error_count' => $dianDocuments->count() + $electronicInvoices->count(),
            'dian_error_items' => $dianDocuments->map(fn ($item) => [
                'source' => 'dian_documents',
                'invoice_id' => $item->invoice_id,
                'status' => $item->dian_status,
                'updated_at' => optional($item->updated_at)->toDateTimeString(),
            ])->concat($electronicInvoices->map(fn ($item) => [
                'source' => 'electronic_invoices',
                'invoice_id' => $item->invoice_id,
                'status' => $item->dian_status,
                'updated_at' => optional($item->updated_at)->toDateTimeString(),
            ]))->sortByDesc('updated_at')->take(10)->values(),
        ];
    }

    private function getSalesLast14Days(Carbon $toDate): array
    {
        $start = $toDate->copy()->subDays(13)->startOfDay();
        $end = $toDate->copy()->endOfDay();

        $query = Invoice::query()
            ->whereBetween('issued_at', [$start, $end])
            ->whereNotNull('issued_at')
            ->whereNotIn('status', ['draft', 'void', 'cancelled']);

        if ($tenantId = $this->tenantId()) {
            $query->where('tenant_id', $tenantId);
        }

        $rows = $query
            ->selectRaw('DATE(issued_at) as sale_date')
            ->selectRaw('SUM(COALESCE(total, grand_total)) as total_sales')
            ->groupBy('sale_date')
            ->pluck('total_sales', 'sale_date');

        $series = [];
        foreach (CarbonPeriod::create($start, '1 day', $toDate->copy()->startOfDay()) as $day) {
            $key = $day->toDateString();
            $series[] = [
                'date' => $key,
                'total' => (float) ($rows[$key] ?? 0),
            ];
        }

        return $series;
    }

    private function tenantId(): ?int
    {
        return Auth::user()?->tenant_id;
    }
}
