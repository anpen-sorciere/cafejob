<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\ChatRoom;
use App\Models\UserApplicationBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\AccessLogger;

class ApplicationController extends Controller
{
    /**
     * 応募処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', '応募するにはログインが必要です。');
        }

        $job = Job::with('shop')->findOrFail($request->job_id);

        // 応募禁止期間中かチェック
        $activeBan = UserApplicationBan::where('user_id', Auth::id())
            ->where('shop_id', $job->shop_id)
            ->where('status', 'active')
            ->where('banned_until', '>', now())
            ->first();

        if ($activeBan) {
            return back()->with('error', 'この店舗への応募は' . $activeBan->banned_until->format('Y年m月d日') . 'まで禁止されています。');
        }

        // 既に応募済みかチェック
        $existingApplication = Application::where('job_id', $request->job_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'この求人には既に応募済みです。');
        }

        // 応募データを作成
        $application = Application::create([
            'job_id' => $request->job_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        // チャットルームを自動作成
        ChatRoom::firstOrCreate(
            [
                'application_id' => $application->id,
            ],
            [
                'shop_id' => $job->shop_id,
                'user_id' => Auth::id(),
                'status' => 'active',
            ]
        );

        // 応募完了を記録
        AccessLogger::logApplyComplete($request, $job->id, $job->shop_id, Auth::id());

        return back()->with('success', '応募が完了しました。店舗からの連絡をお待ちください。');
    }

    /**
     * 応募履歴一覧
     */
    public function index()
    {
        $applications = Application::with(['job.shop.prefecture', 'job.shop.city'])
            ->where('user_id', Auth::id())
            ->orderBy('applied_at', 'desc')
            ->get();

        // TODO: 未読メッセージ数の取得（チャット機能実装時に追加）

        return view('applications.index', compact('applications'));
    }

    /**
     * 応募キャンセル
     */
    public function cancel(Request $request, $id)
    {
        $application = Application::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($application->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => '審査中の応募のみキャンセルできます。',
            ], 400);
        }

        $application->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'success' => true,
            'message' => '応募をキャンセルしました。',
        ]);
    }

    /**
     * 応募ボタンクリックを記録
     */
    public function logClick(Request $request)
    {
        // 認証チェック（ログインしていなくても記録は可能だが、通常はログインユーザーのみ）
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
        ]);

        $job = Job::findOrFail($request->job_id);

        // 応募ボタンクリックを記録
        AccessLogger::logApplyClick($request, $job->id, $job->shop_id);

        return response()->json(['success' => true]);
    }
}
