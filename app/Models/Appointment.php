<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    public const STATUSES = [
        'scheduled',
        'confirmed',
        'in_progress',
        'done',
        'cancelled',
        'no_show',
    ];

    public const SERVICE_TYPES = [
        'grooming',
        'consult',
        'sale',
        'other',
    ];

    protected $fillable = [
        'tenant_id',
        'code',
        'customer_id',
        'pet_id',
        'service_type',
        'start_at',
        'end_at',
        'assigned_to_user_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForTenant(Builder $query, ?int $tenantId): Builder
    {
        return $tenantId ? $query->where('tenant_id', $tenantId) : $query;
    }

    public function scopeForDate(Builder $query, ?string $date): Builder
    {
        return $date ? $query->whereDate('start_at', $date) : $query;
    }

    public function scopeForDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('start_at', [$from, $to]);
    }

    public function scopeForGroomer(Builder $query, ?int $groomerId): Builder
    {
        return $groomerId ? $query->where('assigned_to_user_id', $groomerId) : $query;
    }

    public function scopeForStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeForService(Builder $query, ?string $serviceType): Builder
    {
        return $serviceType ? $query->where('service_type', $serviceType) : $query;
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to_user_id', $userId);
    }
}
