<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCatalog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_catalog';

    protected $fillable = ['tenant_id', 'created_by', 'name', 'service_type', 'description', 'base_price', 'estimated_minutes', 'is_active'];

    public function appointments(): HasMany { return $this->hasMany(Appointment::class, 'service_id'); }
}
