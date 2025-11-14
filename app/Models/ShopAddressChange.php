<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopAddressChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'old_postal_code',
        'old_prefecture_id',
        'old_city_id',
        'old_address',
        'new_postal_code',
        'new_prefecture_id',
        'new_city_id',
        'new_address',
        'verification_code',
        'failed_attempts',
        'is_locked',
        'locked_at',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * 店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 旧都道府県
     */
    public function oldPrefecture()
    {
        return $this->belongsTo(Prefecture::class, 'old_prefecture_id');
    }

    /**
     * 旧市区町村
     */
    public function oldCity()
    {
        return $this->belongsTo(City::class, 'old_city_id');
    }

    /**
     * 新都道府県
     */
    public function newPrefecture()
    {
        return $this->belongsTo(Prefecture::class, 'new_prefecture_id');
    }

    /**
     * 新市区町村
     */
    public function newCity()
    {
        return $this->belongsTo(City::class, 'new_city_id');
    }
}

