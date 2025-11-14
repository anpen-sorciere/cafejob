<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'shop_id',
        'user_id',
        'status',
    ];

    /**
     * 応募
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

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

    /**
     * 店舗管理者（送信者として）
     */
    public function shopAdmin()
    {
        return $this->belongsTo(ShopAdmin::class, 'sender_id')
            ->where('sender_type', 'shop_admin');
    }

    /**
     * メッセージ
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }
}

