<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'created_by',
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
        'notes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'birthdate' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tutors(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_pet')
            ->withPivot(['tenant_id', 'relationship', 'is_primary', 'created_by'])
            ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! filled($term)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term) {
            $builder->where('name', 'like', "%{$term}%")
                ->orWhere('species', 'like', "%{$term}%")
                ->orWhere('breed', 'like', "%{$term}%")
                ->orWhereHas('tutors', function (Builder $tutors) use ($term) {
                    $tutors->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('document', 'like', "%{$term}%");
                });
        });
    }
}
