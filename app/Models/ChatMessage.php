<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'sender_type',
        'sender_id',
        'message',
        'message_type',
        'file_path',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * チャットルーム
     */
    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }
}

