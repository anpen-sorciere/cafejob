<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'user_id',
        'message',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    /**
     * 求人
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * ユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * チャットルーム
     */
    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class);
    }
}

