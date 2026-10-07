<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id_order';

    public const STATUS_MENUNGGU_KONFIRMASI = 'MENUNGGU_KONFIRMASI';
    public const STATUS_DRIVER_DITUGASKAN = 'DRIVER_DITUGASKAN';
    public const STATUS_LAUNDRY_DIAMBIL = 'LAUNDRY_DIAMBIL';
    public const STATUS_SAMPAI_OUTLET = 'SAMPAI_OUTLET';
    public const STATUS_MENUNGGU_PEMBAYARAN = 'MENUNGGU_PEMBAYARAN';
    public const STATUS_DIBAYAR = 'DIBAYAR';
    public const STATUS_SEDANG_DIPROSES = 'SEDANG_DIPROSES';
    public const STATUS_SIAP_DIANTAR = 'SIAP_DIANTAR';
    public const STATUS_MENUNGGU_PENGANTARAN = 'MENUNGGU_PENGANTARAN';
    public const STATUS_SELESAI = 'SELESAI';

    public const STATUSES = [
        self::STATUS_MENUNGGU_KONFIRMASI => 'Menunggu Konfirmasi',
        self::STATUS_DRIVER_DITUGASKAN => 'Driver Ditugaskan',
        self::STATUS_LAUNDRY_DIAMBIL => 'Laundry Diambil',
        self::STATUS_SAMPAI_OUTLET => 'Sampai di Outlet',
        self::STATUS_MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
        self::STATUS_DIBAYAR => 'Dibayar',
        self::STATUS_SEDANG_DIPROSES => 'Sedang Diproses',
        self::STATUS_SIAP_DIANTAR => 'Siap Diantar',
        self::STATUS_MENUNGGU_PENGANTARAN => 'Menunggu Pengantaran',
        self::STATUS_SELESAI => 'Selesai',
    ];

    protected $fillable = [
        'id_user',
        'order_code',
        'customer_name',
        'customer_phone',
        'address_text',
        'latitude',
        'longitude',
        'distance_km',
        'notes',
        'pickup_schedule',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'distance_km' => 'decimal:2',
            'pickup_schedule' => 'datetime',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'id_order', 'id_order');
    }

    public function pickupAssignment(): HasOne
    {
        return $this->hasOne(Assignment::class, 'id_order', 'id_order')
            ->where('type', 'pickup')
            ->latestOfMany('id_assignment');
    }

    public function deliveryAssignment(): HasOne
    {
        return $this->hasOne(Assignment::class, 'id_order', 'id_order')
            ->where('type', 'delivery')
            ->latestOfMany('id_assignment');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'id_order', 'id_order');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'id_order', 'id_order')->latestOfMany('id_invoice');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'id_order', 'id_order')->orderBy('changed_at', 'asc');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'id_order', 'id_order');
    }
}
