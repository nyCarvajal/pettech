<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'species',
        'breed',
        'size',
        'birthdate',
        'sex',
        'color',
        'allergies',
        'behavior_notes',
        'grooming_preferences',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'birthdate' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
