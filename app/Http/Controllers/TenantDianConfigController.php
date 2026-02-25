<?php

namespace App\Http\Controllers;

use App\Models\TenantDianConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantDianConfigController extends Controller
{
    public function edit()
    {
        $tenantId = (int) Auth::user()->tenant_id;

        $config = TenantDianConfig::query()->firstOrNew([
            'tenant_id' => $tenantId,
        ]);

        return view('dian.config.edit', compact('config'));
    }

    public function update(Request $request): RedirectResponse
    {
        $tenantId = (int) Auth::user()->tenant_id;

        $validated = $request->validate([
            'software_id' => ['required', 'string', 'max:255'],
            'pin' => ['required', 'string', 'max:255'],
            'certificate_path' => ['required', 'string', 'max:255'],
            'certificate_password' => ['required', 'string', 'max:255'],
            'environment' => ['required', 'in:test,prod'],
            'resolution_number' => ['required', 'string', 'max:255'],
            'prefix' => ['required', 'string', 'max:20'],
            'range_from' => ['required', 'integer', 'min:1'],
            'range_to' => ['required', 'integer', 'gte:range_from'],
        ]);

        TenantDianConfig::query()->updateOrCreate(
            ['tenant_id' => $tenantId],
            $validated
        );

        return back()->with('status', 'Configuración DIAN guardada correctamente.');
    }
}
