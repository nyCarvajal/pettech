<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'category_id', 'sku', 'name', 'unit', 'cost_price', 'sale_price', 'min_stock', 'is_active'];

    public function category(): BelongsTo { return $this->belongsTo(ProductCategory::class, 'category_id'); }
    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class); }
}
