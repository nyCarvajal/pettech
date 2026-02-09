<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'created_by',
        'invoice_id',
        'method',
        'amount',
        'paid_at',
        'reference',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
