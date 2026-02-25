<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroomingStageLog extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'grooming_session_id',
        'stage',
        'changed_at',
        'changed_by',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(GroomingSession::class, 'grooming_session_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
