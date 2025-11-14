<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefecture_id',
        'name',
    ];

    /**
     * 都道府県
     */
    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 店舗
     */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}

