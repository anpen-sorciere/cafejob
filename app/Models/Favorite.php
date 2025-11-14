<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'job_id',
    ];

    /**
     * ユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 求人
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}

