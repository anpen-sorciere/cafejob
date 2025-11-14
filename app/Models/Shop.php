<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'postal_code',
        'address',
        'prefecture_id',
        'city_id',
        'phone',
        'email',
        'website',
        'opening_hours',
        'concept_type',
        'uniform_type',
        'image_url',
        'atmosphere_images',
        'job_features',
        'status',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'atmosphere_images' => 'array',
    ];

    /**
     * 都道府県
     */
    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 市区町村
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * 求人
     */
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    /**
     * キャスト
     */
    public function casts()
    {
        return $this->hasMany(Cast::class);
    }

    /**
     * 口コミ
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * 店舗管理者
     */
    public function shopAdmins()
    {
        return $this->hasMany(ShopAdmin::class);
    }
}

