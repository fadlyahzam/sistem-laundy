<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $primaryKey = 'id_invoice';

    protected $fillable = [
        'id_order',
        'invoice_number',
        'total_amount',
        'status',
        'issued_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'issued_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_invoice', 'id_invoice');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'id_invoice', 'id_invoice')->latestOfMany('id_payment');
    }
}
