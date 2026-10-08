<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignments';
    protected $primaryKey = 'id_assignment';

    protected $fillable = [
        'id_order',
        'id_driver',
        'type',
        'status',
        'pickup_photo',
        'delivery_photo',
        'proof_photo',
        'assigned_at',
        'finished_at',
    ];

    public function getProofPhotoUrlAttribute(): ?string
    {
        $photo = $this->proof_photo ?? ($this->type === 'pickup' ? $this->pickup_photo : $this->delivery_photo);
        if ($photo) {
            return str_starts_with($photo, 'http') ? $photo : asset('storage/' . $photo);
        }
        return null;
    }

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'id_driver', 'id_driver');
    }
}
