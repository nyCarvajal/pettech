<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    public function index()
    {
        $this->ensureTenantDatabaseLoaded();

        $warehouses = Warehouse::on('tenant')->orderBy('name')->paginate(15);
        return view('inventory.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $this->ensureTenantDatabaseLoaded();

        return view('inventory.warehouses.create');
    }

    public function store(Request $request)
    {
        $this->ensureTenantDatabaseLoaded();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:40', 'unique:tenant.warehouses,code'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_main' => ['nullable', 'boolean'],
        ]);
        $data['is_main'] = (bool)($data['is_main'] ?? false);
        Warehouse::on('tenant')->create($data);
        return redirect()->route('warehouses.index')->with('status', 'Bodega creada.');
    }

    public function edit(int $warehouse)
    {
        $this->ensureTenantDatabaseLoaded();

        $warehouse = Warehouse::on('tenant')->findOrFail($warehouse);

        return view('inventory.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, int $warehouse)
    {
        $this->ensureTenantDatabaseLoaded();

        $warehouse = Warehouse::on('tenant')->findOrFail($warehouse);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:40', 'unique:tenant.warehouses,code,'.$warehouse->id],
            'location' => ['nullable', 'string', 'max:255'],
            'is_main' => ['nullable', 'boolean'],
        ]);
        $data['is_main'] = (bool)($data['is_main'] ?? false);
        $warehouse->update($data);
        return redirect()->route('warehouses.index')->with('status', 'Bodega actualizada.');
    }

    public function destroy(int $warehouse)
    {
        $this->ensureTenantDatabaseLoaded();

        $warehouse = Warehouse::on('tenant')->findOrFail($warehouse);
        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('status', 'Bodega eliminada.');
    }

    private function ensureTenantDatabaseLoaded(): void
    {
        if (filled(DB::connection('tenant')->getDatabaseName())) {
            return;
        }

        $user = Auth::user();

        $database = $this->resolveTenantDatabaseName($user);

        if (filled($database)) {
            config(['database.connections.tenant.database' => $database]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        if (blank(DB::connection('tenant')->getDatabaseName())) {
            Log::error('No se pudo inicializar la base de datos tenant.', [
                'controller' => static::class,
                'user_id' => $user?->id,
                'requested_tenant_db' => $database,
                'configured_tenant_db' => config('database.connections.tenant.database'),
                'route' => request()->path(),
            ]);

            abort(500, 'No se pudo inicializar la base de datos tenant. Revisa storage/logs/laravel.log para más detalles.');
        }
    }
}
