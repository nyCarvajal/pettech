<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'client_id',
        'customer_id',
        'pet_id',
        'appointment_id',
        'invoice_number',
        'number',
        'invoice_type',
        'status',
        'issued_at',
        'subtotal',
        'tax_total',
        'discount_total',
        'grand_total',
        'total',
        'notes',
        'currency',
        'inventory_applied_at',
    ];

    protected $casts = ['issued_at' => 'datetime', 'inventory_applied_at' => 'datetime'];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Client::class, 'customer_id'); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(InvoicePayment::class); }
    public function posPayments(): HasMany { return $this->hasMany(Payment::class); }
    public function dianDocument(): HasOne { return $this->hasOne(DianDocument::class); }
    public function electronicInvoice(): HasOne { return $this->hasOne(ElectronicInvoice::class); }
}
