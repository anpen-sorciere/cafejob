<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ShopAdmin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'shop_admins';

    protected $fillable = [
        'shop_id',
        'username',
        'email',
        'password_hash',
        'status',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Check if shop admin is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Relationship with Shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

