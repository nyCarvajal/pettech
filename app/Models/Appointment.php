<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'client_id', 'pet_id', 'service_id', 'groomer_user_id', 'scheduled_start', 'scheduled_end', 'status', 'channel', 'notes'];

    protected $casts = ['scheduled_start' => 'datetime', 'scheduled_end' => 'datetime'];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function service(): BelongsTo { return $this->belongsTo(ServiceCatalog::class, 'service_id'); }
    public function groomer(): BelongsTo { return $this->belongsTo(User::class, 'groomer_user_id'); }
    public function statusLogs(): HasMany { return $this->hasMany(AppointmentStatusLog::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
}
