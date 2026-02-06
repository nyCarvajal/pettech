<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'created_by', 'client_id', 'appointment_id', 'invoice_number', 'invoice_type', 'status', 'issued_at', 'subtotal', 'tax_total', 'discount_total', 'grand_total', 'currency'];

    protected $casts = ['issued_at' => 'datetime', 'inventory_applied_at' => 'datetime'];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(InvoicePayment::class); }
    public function dianDocument(): HasOne { return $this->hasOne(DianDocument::class); }
}
