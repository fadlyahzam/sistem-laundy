<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isPelanggan(): bool
    {
        return $this->role === 'pelanggan';
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class, 'id_user', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'id_user', 'id_user');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'id_user', 'id_user');
    }
}
