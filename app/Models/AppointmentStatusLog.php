<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentStatusLog extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'appointment_id', 'from_status', 'to_status', 'comment'];

    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
}
