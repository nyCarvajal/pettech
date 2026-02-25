<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroomingSession extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    public const STAGES = [
        'received',
        'washing',
        'drying',
        'trimming',
        'finishing',
        'ready',
    ];

    protected $fillable = [
        'appointment_id',
        'tenant_id',
        'groomer_user_id',
        'current_stage',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function groomer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'groomer_user_id');
    }

    public function stageLogs(): HasMany
    {
        return $this->hasMany(GroomingStageLog::class);
    }

    public function getStageIndexAttribute(): int
    {
        return array_search($this->current_stage, self::STAGES, true) ?: 0;
    }
}
