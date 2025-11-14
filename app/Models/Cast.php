<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'nickname',
        'age',
        'height',
        'blood_type',
        'hobby',
        'special_skill',
        'profile_image',
        'status',
    ];

    /**
     * 店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

