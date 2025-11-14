<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDeleteImageController extends Controller
{
    /**
     * 画像削除
     */
    public function destroy($id)
    {
        $message = ChatMessage::where('id', $id)
            ->where('message_type', 'image')
            ->whereNotNull('file_path')
            ->firstOrFail();

        // ファイルを削除
        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }

        // 通知も削除
        ChatNotification::where('message_id', $id)->delete();

        // メッセージを削除
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => '画像を削除しました'
        ]);
    }
}

