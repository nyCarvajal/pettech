<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Clinica;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function resolveTenantDatabaseName(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if (filled($user->db)) {
            return $user->db;
        }

        if (filled($user->pet_id) && Schema::hasTable('pets') && Schema::hasColumn('pets', 'db')) {
            return DB::table('pets')->where('id', $user->pet_id)->value('db');
        }

        return null;
    }
	
	public function __construct()
    {
        // Este closure se ejecuta antes de cada método de cualquier controlador hijo
        $this->middleware(function ($request, $next) {
            if ($user = Auth::user()) {
                $peluqueria = Clinica::resolveForUser($user);
                $database = $peluqueria->db ?? $this->resolveTenantDatabaseName($user);

                if (! $database) {
                    return $next($request);
                }

                // Inyecta la base tenant y la hace default
                config(['database.connections.tenant.database' => $database]);
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');
            }

            return $next($request);
        });
    }
}
