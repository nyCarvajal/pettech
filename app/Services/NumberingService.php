<?php

namespace App\Services;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    public function nextNumber(int $tenantId, string $key = 'invoice'): string
    {
        return DB::transaction(function () use ($tenantId, $key) {
            $sequence = Sequence::query()
                ->where('tenant_id', $tenantId)
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = Sequence::query()->create([
                    'tenant_id' => $tenantId,
                    'key' => $key,
                    'current_number' => 0,
                ]);
            }

            $sequence->current_number += 1;
            $sequence->save();

            return (string) $sequence->current_number;
        });
    }
}
