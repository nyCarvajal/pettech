<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'invoice_id', 'item_type', 'product_id', 'service_id', 'description', 'quantity', 'unit_price', 'tax_rate', 'discount_rate', 'line_total'];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function service(): BelongsTo { return $this->belongsTo(ServiceCatalog::class, 'service_id'); }
}
