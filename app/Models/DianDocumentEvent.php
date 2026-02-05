<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DianDocumentEvent extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'dian_document_id', 'event', 'payload', 'event_at'];

    protected $casts = ['event_at' => 'datetime'];

    public function dianDocument(): BelongsTo { return $this->belongsTo(DianDocument::class); }
}
