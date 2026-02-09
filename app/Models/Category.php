<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
