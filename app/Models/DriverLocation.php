<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverLocation extends Model
{
    use HasFactory;

    protected $table = 'driver_locations';
    protected $primaryKey = 'id_location';

    public const CREATED_AT = null;
    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_driver',
        'latitude',
        'longitude',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'updated_at' => 'datetime',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'id_driver', 'id_driver');
    }
}
