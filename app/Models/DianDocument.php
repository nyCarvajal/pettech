<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DianDocument extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'invoice_id', 'environment', 'document_type', 'cufe', 'xml_path', 'zip_path', 'qr_data', 'dian_status', 'validation_code', 'status_message', 'sent_at', 'validated_at'];

    protected $casts = ['sent_at' => 'datetime', 'validated_at' => 'datetime'];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function events(): HasMany { return $this->hasMany(DianDocumentEvent::class); }
}
