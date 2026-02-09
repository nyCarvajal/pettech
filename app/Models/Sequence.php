<?php

namespace App\Models;

class Sequence extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'key',
        'current_number',
    ];
}
