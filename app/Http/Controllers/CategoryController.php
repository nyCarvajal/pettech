<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $this->ensureTenantDatabaseLoaded();

        $categories = Category::on('tenant')->orderBy('name')->paginate(15);
        return view('inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->ensureTenantDatabaseLoaded();

        return view('inventory.categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureTenantDatabaseLoaded();

        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:tenant.categories,name']]);
        Category::on('tenant')->create($data);
        return redirect()->route('categories.index')->with('status', 'Categoría creada.');
    }

    public function edit(int $category)
    {
        $this->ensureTenantDatabaseLoaded();

        $category = Category::on('tenant')->findOrFail($category);

        return view('inventory.categories.edit', compact('category'));
    }

    public function update(Request $request, int $category)
    {
        $this->ensureTenantDatabaseLoaded();

        $category = Category::on('tenant')->findOrFail($category);

        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:tenant.categories,name,'.$category->id]]);
        $category->update($data);
        return redirect()->route('categories.index')->with('status', 'Categoría actualizada.');
    }

    public function destroy(int $category)
    {
        $this->ensureTenantDatabaseLoaded();

        $category = Category::on('tenant')->findOrFail($category);
        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Categoría eliminada.');
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
