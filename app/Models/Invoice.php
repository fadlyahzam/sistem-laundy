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

    public function getTerbilangAttribute(): string
    {
        $amount = (int) round((float) $this->total_amount);
        if ($amount <= 0) {
            return 'Nol Rupiah';
        }
        return trim(self::numberToWords($amount)) . ' Rupiah';
    }

    public static function numberToWords(int $number): string
    {
        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if ($number < 12) {
            return $words[$number];
        } elseif ($number < 20) {
            return self::numberToWords($number - 10) . ' Belas';
        } elseif ($number < 100) {
            return self::numberToWords(intdiv($number, 10)) . ' Puluh ' . self::numberToWords($number % 10);
        } elseif ($number < 200) {
            return 'Seratus ' . self::numberToWords($number - 100);
        } elseif ($number < 1000) {
            return self::numberToWords(intdiv($number, 100)) . ' Ratus ' . self::numberToWords($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . self::numberToWords($number - 1000);
        } elseif ($number < 1000000) {
            return self::numberToWords(intdiv($number, 1000)) . ' Ribu ' . self::numberToWords($number % 1000);
        } elseif ($number < 1000000000) {
            return self::numberToWords(intdiv($number, 1000000)) . ' Juta ' . self::numberToWords($number % 1000000);
        } elseif ($number < 1000000000000) {
            return self::numberToWords(intdiv($number, 1000000000)) . ' Miliar ' . self::numberToWords($number % 1000000000);
        }
        return (string) $number;
    }
}
