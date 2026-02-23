<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function edit(Warehouse $warehouse)
    {
        $this->ensureTenantDatabaseLoaded();

        return view('inventory.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->ensureTenantDatabaseLoaded();

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

    public function destroy(Warehouse $warehouse)
    {
        $this->ensureTenantDatabaseLoaded();

        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('status', 'Bodega eliminada.');
    }

    private function ensureTenantDatabaseLoaded(): void
    {
        if (filled(DB::connection('tenant')->getDatabaseName())) {
            return;
        }

        $user = Auth::user();

        if ($user && filled($user->db)) {
            config(['database.connections.tenant.database' => $user->db]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        abort_if(blank(DB::connection('tenant')->getDatabaseName()), 500, 'No se pudo inicializar la base de datos tenant.');
    }
}
