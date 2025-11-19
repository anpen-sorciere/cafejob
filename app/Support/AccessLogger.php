<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccessLogger
{
    /**
     * イベントタイプ定数
     */
    const EVENT_PAGE_VIEW = 'page_view';
    const EVENT_JOB_VIEW = 'job_view';
    const EVENT_APPLY_CLICK = 'apply_click';
    const EVENT_APPLY_COMPLETE = 'apply_complete';
    const EVENT_KEEP_ADD = 'keep_add'; // 将来実装用
    
    /**
     * ページビューを記録
     * 
     * @param Request $request
     * @param int|null $shopId
     * @param int|null $jobId
     * @return void
     */
    public static function logPageView(Request $request, ?int $shopId = null, ?int $jobId = null)
    {
        $eventType = $jobId ? self::EVENT_JOB_VIEW : self::EVENT_PAGE_VIEW;
        
        self::log($request, $eventType, [
            'shop_id' => $shopId,
            'job_id' => $jobId,
        ]);
    }
    
    /**
     * 求人詳細ページビューを記録
     * 
     * @param Request $request
     * @param int $jobId
     * @param int|null $shopId
     * @return void
     */
    public static function logJobView(Request $request, int $jobId, ?int $shopId = null)
    {
        self::log($request, self::EVENT_JOB_VIEW, [
            'shop_id' => $shopId,
            'job_id' => $jobId,
        ]);
    }
    
    /**
     * 応募ボタンクリックを記録
     * 
     * @param Request $request
     * @param int $jobId
     * @param int|null $shopId
     * @return void
     */
    public static function logApplyClick(Request $request, int $jobId, ?int $shopId = null)
    {
        self::log($request, self::EVENT_APPLY_CLICK, [
            'shop_id' => $shopId,
            'job_id' => $jobId,
        ]);
    }
    
    /**
     * 応募完了を記録
     * 
     * @param Request $request
     * @param int $jobId
     * @param int|null $shopId
     * @param int|null $userId
     * @return void
     */
    public static function logApplyComplete(Request $request, int $jobId, ?int $shopId = null, ?int $userId = null)
    {
        self::log($request, self::EVENT_APPLY_COMPLETE, [
            'shop_id' => $shopId,
            'job_id' => $jobId,
            'user_id' => $userId,
        ]);
    }
    
    /**
     * イベントログを記録（内部メソッド）
     * 
     * @param Request $request
     * @param string $eventType
     * @param array $additionalData
     * @return void
     */
    private static function log(Request $request, string $eventType, array $additionalData = [])
    {
        try {
            // ユーザーIDを取得
            $userId = auth()->check() ? auth()->id() : null;
            
            // セッションIDを取得
            $sessionId = $request->session()->getId();
            
            // リファラを取得
            $referrer = $request->header('referer');
            if (strlen($referrer) > 255) {
                $referrer = substr($referrer, 0, 255);
            }
            
            // UTMパラメータを取得
            $utmSource = $request->get('utm_source');
            $utmMedium = $request->get('utm_medium');
            $utmCampaign = $request->get('utm_campaign');
            
            // デバイス判定（簡易版）
            $userAgent = $request->userAgent();
            $device = self::detectDevice($userAgent);
            
            // IPアドレスをハッシュ化（プライバシー配慮）
            $ipAddress = $request->ip();
            $ipHash = $ipAddress ? hash('sha256', $ipAddress) : null;
            
            // 現在時刻（JST）
            $occurredAt = Carbon::now('Asia/Tokyo');
            
            DB::table('event_logs')->insert([
                'occurred_at' => $occurredAt,
                'event_type' => $eventType,
                'user_id' => $additionalData['user_id'] ?? $userId,
                'shop_id' => $additionalData['shop_id'] ?? null,
                'job_id' => $additionalData['job_id'] ?? null,
                'session_id' => $sessionId,
                'referrer' => $referrer,
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'device' => $device,
                'ip_hash' => $ipHash,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // ログ記録の失敗はアプリケーションの動作に影響を与えないようにする
            \Log::error('AccessLogger: Failed to log event', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * デバイスを判定（簡易版）
     * 
     * @param string|null $userAgent
     * @return string
     */
    private static function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        $userAgentLower = strtolower($userAgent);
        
        // スマートフォン判定
        if (preg_match('/(iphone|ipod|android|mobile|blackberry|windows phone)/', $userAgentLower)) {
            return 'sp';
        }
        
        // タブレット判定
        if (preg_match('/(ipad|tablet)/', $userAgentLower)) {
            return 'tablet';
        }
        
        // PC判定
        return 'pc';
    }
}

