<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'id_layanan',
        'name',
        'unit_tariff',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_tariff' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_kategori', 'id_kategori');
    }
}
