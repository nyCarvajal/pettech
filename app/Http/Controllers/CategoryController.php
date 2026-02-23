<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        return view('inventory.categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureTenantDatabaseLoaded();

        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:tenant.categories,name']]);
        Category::on('tenant')->create($data);
        return redirect()->route('categories.index')->with('status', 'Categoría creada.');
    }

    public function edit(Category $category)
    {
        $this->ensureTenantDatabaseLoaded();

        return view('inventory.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->ensureTenantDatabaseLoaded();

        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:tenant.categories,name,'.$category->id]]);
        $category->update($data);
        return redirect()->route('categories.index')->with('status', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        $this->ensureTenantDatabaseLoaded();

        $category->delete();
        return redirect()->route('categories.index')->with('status', 'Categoría eliminada.');
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
