<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';

    protected $fillable = [
        'name',
        'service_type',
        'price_per_kg',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function kategori(): HasMany
    {
        return $this->hasMany(Kategori::class, 'id_layanan', 'id_layanan');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_layanan', 'id_layanan');
    }
}
