<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDianConfig extends BaseModel
{
    protected $table = 'tenant_dian_config';

    protected $fillable = [
        'tenant_id',
        'software_id',
        'pin',
        'certificate_path',
        'certificate_password',
        'environment',
        'resolution_number',
        'prefix',
        'range_from',
        'range_to',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
