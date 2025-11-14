<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'user_id',
        'rating',
        'title',
        'content',
        'comment', // 旧スキーマとの互換性
        'status',
    ];

    /**
     * 店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * ユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

