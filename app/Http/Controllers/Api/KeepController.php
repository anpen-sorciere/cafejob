<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Job;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeepController extends Controller
{
    /**
     * キープのトグル
     */
    public function toggle(Request $request)
    {
        // Feature flagチェック
        if (!config('feature_flags.keep', false)) {
            return response()->json([
                'success' => false,
                'message' => '機能が無効化されています。',
                'kept' => false,
            ], 403);
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'ログインが必要です。',
                'requireLogin' => true,
                'kept' => false,
            ], 401);
        }

        $user = Auth::user();
        $jobId = $request->input('job_id') ? (int)$request->input('job_id') : null;
        $shopId = $request->input('shop_id') ? (int)$request->input('shop_id') : null;
        $action = $request->input('action', 'toggle');

        // バリデーション: job_id と shop_id のどちらか一方のみ
        if ((!$jobId && !$shopId) || ($jobId && $shopId)) {
            return response()->json([
                'success' => false,
                'message' => '対象が正しくありません。',
                'kept' => false,
            ], 400);
        }

        try {
            if ($jobId) {
                // 求人の存在確認
                $job = Job::where('id', $jobId)
                    ->where('status', 'active')
                    ->first();

                if (!$job) {
                    return response()->json([
                        'success' => false,
                        'message' => '求人が見つかりません。',
                        'kept' => false,
                    ], 404);
                }

                $favorite = Favorite::where('user_id', $user->id)
                    ->where('job_id', $jobId)
                    ->first();

                $currentlyKept = $favorite !== null;
                $shouldAdd = $action === 'add' || ($action === 'toggle' && !$currentlyKept);

                if ($shouldAdd) {
                    if (!$favorite) {
                        Favorite::create([
                            'user_id' => $user->id,
                            'job_id' => $jobId,
                        ]);
                    }
                    $kept = true;
                } else {
                    if ($favorite) {
                        $favorite->delete();
                    }
                    $kept = false;
                }
            } else {
                // 店舗の存在確認
                $shop = Shop::where('id', $shopId)
                    ->where('status', 'active')
                    ->first();

                if (!$shop) {
                    return response()->json([
                        'success' => false,
                        'message' => '店舗が見つかりません。',
                        'kept' => false,
                    ], 404);
                }

                $favorite = Favorite::where('user_id', $user->id)
                    ->where('shop_id', $shopId)
                    ->first();

                $currentlyKept = $favorite !== null;
                $shouldAdd = $action === 'add' || ($action === 'toggle' && !$currentlyKept);

                if ($shouldAdd) {
                    if (!$favorite) {
                        Favorite::create([
                            'user_id' => $user->id,
                            'shop_id' => $shopId,
                        ]);
                    }
                    $kept = true;
                } else {
                    if ($favorite) {
                        $favorite->delete();
                    }
                    $kept = false;
                }
            }

            return response()->json([
                'success' => true,
                'kept' => $kept,
                'message' => $kept ? 'キープに追加しました。' : 'キープから削除しました。',
            ]);
        } catch (\Exception $e) {
            \Log::error('keep_toggle error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'job_id' => $jobId,
                'shop_id' => $shopId,
            ]);
            
            $errorMessage = 'エラーが発生しました。';
            if (config('app.debug')) {
                $errorMessage .= ' (' . $e->getMessage() . ')';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'kept' => false,
            ], 500);
        }
    }
}

