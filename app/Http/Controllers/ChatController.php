<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * チャットルーム一覧を表示
     */
    public function index()
    {
        $user = Auth::user();
        
        $chatRooms = ChatRoom::where('user_id', $user->id)
            ->with(['shop', 'application.job'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_type', 'shop_admin')
                      ->where('is_read', false);
            }])
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($room) {
                $lastMessage = $room->messages->first();
                return [
                    'id' => $room->id,
                    'shop_name' => $room->shop->name,
                    'job_title' => $room->application->job->title,
                    'application_status' => $room->application->status,
                    'unread_count' => $room->unread_count,
                    'last_message' => $lastMessage ? $lastMessage->message : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                ];
            });

        return view('chat.index', compact('chatRooms'));
    }

    /**
     * チャット詳細を表示
     */
    public function show(Request $request, $id = null)
    {
        $user = Auth::user();
        $roomId = $id ?? $request->get('room_id');
        $applicationId = $request->get('application_id');

        // application_idが指定されている場合（応募画面から直接アクセス）
        if ($applicationId) {
            $application = Application::where('id', $applicationId)
                ->where('user_id', $user->id)
                ->with('job')
                ->first();

            if (!$application) {
                return redirect()->route('applications.index')
                    ->with('error', '応募が見つかりません。');
            }

            // 既存のチャットルームをチェック
            $room = ChatRoom::where('application_id', $applicationId)
                ->where('user_id', $user->id)
                ->with(['shop', 'application.job'])
                ->first();

            if (!$room) {
                // チャットルームを作成
                $room = ChatRoom::create([
                    'shop_id' => $application->job->shop_id,
                    'user_id' => $user->id,
                    'application_id' => $applicationId,
                    'status' => 'active',
                ]);
                $room->load(['shop', 'application.job']);
            }
        } else if ($roomId) {
            // room_idが指定されている場合（チャット一覧からアクセス）
            $room = ChatRoom::where('id', $roomId)
                ->where('user_id', $user->id)
                ->with(['shop', 'application.job'])
                ->first();

            if (!$room) {
                return redirect()->route('chat.index')
                    ->with('error', 'チャットルームが見つかりません。');
            }
        } else {
            return redirect()->route('chat.index');
        }

        // メッセージ送信処理
        if ($request->isMethod('post') && $request->input('action') === 'send_message') {
            return $this->sendMessage($request, $room);
        }

        // ルームのリレーションを読み込む
        $room->load(['user', 'shop.shopAdmins']);

        // メッセージ一覧を取得
        $messages = ChatMessage::where('room_id', $room->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($room) {
                $senderName = null;
                if ($message->sender_type === 'user') {
                    $senderName = $room->user->username ?? 'ユーザー';
                } else {
                    $shopAdmin = $room->shop->shopAdmins->firstWhere('id', $message->sender_id);
                    $senderName = $shopAdmin->username ?? '店舗管理者';
                }
                return [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $senderName,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'file_path' => $message->file_path,
                    'created_at' => $message->created_at,
                ];
            });

        // 未読メッセージを既読にマーク
        ChatMessage::where('room_id', $room->id)
            ->where('sender_type', 'shop_admin')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('chat.show', compact('room', 'messages'));
    }

    /**
     * メッセージを送信
     */
    private function sendMessage(Request $request, ChatRoom $room)
    {
        $user = Auth::user();
        $message = trim($request->input('message', ''));
        $messageType = 'text';
        $filePath = null;

        // 画像アップロード処理
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions) &&
                $file->getSize() <= $maxSize) {
                
                $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('chat', $fileName, 'public');
                $messageType = 'image';
                
                if (empty($message)) {
                    $message = '[画像を送信しました]';
                }
            } else {
                return redirect()->back()
                    ->with('error', '無効なファイル形式またはファイルサイズが大きすぎます。');
            }
        }

        if (empty($message) && $messageType !== 'image') {
            return redirect()->back()
                ->with('error', 'メッセージまたは画像を入力してください。');
        }

        try {
            // メッセージを送信
            ChatMessage::create([
                'room_id' => $room->id,
                'sender_type' => 'user',
                'sender_id' => $user->id,
                'message' => $message,
                'message_type' => $messageType,
                'file_path' => $filePath,
                'is_read' => false,
            ]);

            // ルームの更新時間を更新
            $room->touch();

            return redirect()->route('chat.show.id', ['id' => $room->id])
                ->with('success', 'メッセージを送信しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'メッセージの送信に失敗しました: ' . $e->getMessage());
        }
    }
}

