<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicInvoice extends BaseModel
{
    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'dian_status',
        'cufe',
        'xml_path',
        'response_json',
        'last_error',
        'sent_at',
        'accepted_at',
    ];

    protected $casts = [
        'response_json' => 'array',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
