<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index(Request $request)
    {
        if (blank(DB::connection('tenant')->getDatabaseName()) && ($user = Auth::user()) && filled($user->db)) {
            config(['database.connections.tenant.database' => $user->db]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        if (blank(DB::connection('tenant')->getDatabaseName())) {
            $data = $this->dashboardService->emptyDashboardData(
                $request->string('from')->toString(),
                $request->string('to')->toString(),
            );

            return view('admin.dashboard', $data);
        }

        $data = $this->dashboardService->getDashboardData(
            $request->string('from')->toString(),
            $request->string('to')->toString(),
        );

        return view('admin.dashboard', $data);
    }
}
