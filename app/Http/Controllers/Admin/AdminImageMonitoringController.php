<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class AdminImageMonitoringController extends Controller
{
    /**
     * 画像監視一覧
     */
    public function index()
    {
        $images = ChatMessage::with(['room.user', 'room.shop'])
            ->where('message_type', 'image')
            ->whereNotNull('file_path')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($message) {
                // 送信者情報を取得
                if ($message->sender_type === 'user') {
                    $message->sender_name = $message->room->user->username ?? 'ユーザー';
                } else {
                    $shopAdmin = \App\Models\ShopAdmin::find($message->sender_id);
                    $message->sender_name = $shopAdmin->username ?? '店舗管理者';
                }
                return $message;
            });

        return view('admin.image-monitoring.index', compact('images'));
    }
}

