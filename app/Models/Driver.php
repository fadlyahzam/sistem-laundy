<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';
    protected $primaryKey = 'id_driver';

    protected $fillable = [
        'id_user',
        'plate_number',
        'vehicle_type',
        'availability',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'id_driver', 'id_driver');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(DriverLocation::class, 'id_driver', 'id_driver');
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(DriverLocation::class, 'id_driver', 'id_driver')->latestOfMany('id_location');
    }
}
