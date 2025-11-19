<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Shop;
use App\Models\Job;
use App\Models\Application;

class AggregateDailyMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:aggregate-daily {--date= : 集計日（Y-m-d形式、未指定の場合は前日）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '前日分（JST）のevent_logs, users, shops, jobs, applicationsから各daily_*テーブルへ集計';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 集計日を決定（JST）
        $dateOption = $this->option('date');
        if ($dateOption) {
            $targetDate = Carbon::parse($dateOption)->setTimezone('Asia/Tokyo');
        } else {
            // 前日（JST）
            $targetDate = Carbon::yesterday('Asia/Tokyo');
        }
        
        $dateStr = $targetDate->format('Y-m-d');
        $this->info("集計日: {$dateStr} (JST)");
        
        $startOfDay = $targetDate->copy()->startOfDay();
        $endOfDay = $targetDate->copy()->endOfDay();
        
        try {
            DB::beginTransaction();
            
            // 1. daily_site_metrics の集計
            $this->aggregateSiteMetrics($dateStr, $startOfDay, $endOfDay);
            
            // 2. daily_shop_metrics の集計
            $this->aggregateShopMetrics($dateStr, $startOfDay, $endOfDay);
            
            // 3. daily_job_metrics の集計
            $this->aggregateJobMetrics($dateStr, $startOfDay, $endOfDay);
            
            DB::commit();
            
            $this->info("集計が完了しました。");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("集計中にエラーが発生しました: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
    
    /**
     * サイト全体のメトリクスを集計
     */
    private function aggregateSiteMetrics($dateStr, $startOfDay, $endOfDay)
    {
        $this->info("サイト全体メトリクスを集計中...");
        
        // event_logsから集計
        $pageViews = DB::table('event_logs')
            ->where('event_type', 'page_view')
            ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->count();
            
        $uniqueUsers = DB::table('event_logs')
            ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
            
        $sessions = DB::table('event_logs')
            ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');
            
        $applyClicks = DB::table('event_logs')
            ->where('event_type', 'apply_click')
            ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->count();
            
        $applications = DB::table('event_logs')
            ->where('event_type', 'apply_complete')
            ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->count();
        
        // users, shops, jobsテーブルから新規登録数を集計
        $newUsers = User::whereDate('created_at', $dateStr)->count();
        $newShops = Shop::whereDate('created_at', $dateStr)->count();
        $newJobs = Job::whereDate('created_at', $dateStr)->count();
        
        // 既存レコードがあれば更新、なければ作成
        DB::table('daily_site_metrics')->updateOrInsert(
            ['date' => $dateStr],
            [
                'page_views' => $pageViews,
                'unique_users' => $uniqueUsers,
                'sessions' => $sessions,
                'new_users' => $newUsers,
                'new_shops' => $newShops,
                'new_jobs' => $newJobs,
                'applications' => $applications,
                'apply_clicks' => $applyClicks,
                'updated_at' => now(),
            ]
        );
        
        $this->info("  PV: {$pageViews}, ユニークユーザー: {$uniqueUsers}, 応募完了: {$applications}");
    }
    
    /**
     * 店舗ごとのメトリクスを集計
     */
    private function aggregateShopMetrics($dateStr, $startOfDay, $endOfDay)
    {
        $this->info("店舗メトリクスを集計中...");
        
        // 店舗一覧を取得
        $shops = Shop::pluck('id');
        
        foreach ($shops as $shopId) {
            // 店舗プロフィールページのPV（shop_idがあるがjob_idがないpage_view）
            $shopPageViews = DB::table('event_logs')
                ->where('event_type', 'page_view')
                ->where('shop_id', $shopId)
                ->whereNull('job_id')
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // 求人詳細ページPV合計（job_idがあるpage_view）
            $jobPageViews = DB::table('event_logs')
                ->where('event_type', 'job_view')
                ->where('shop_id', $shopId)
                ->whereNotNull('job_id')
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // 応募ボタンクリック数
            $applyClicks = DB::table('event_logs')
                ->where('event_type', 'apply_click')
                ->where('shop_id', $shopId)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // 応募完了数
            $applications = DB::table('event_logs')
                ->where('event_type', 'apply_complete')
                ->where('shop_id', $shopId)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // キープ追加数（将来実装）
            $keeps = DB::table('event_logs')
                ->where('event_type', 'keep_add')
                ->where('shop_id', $shopId)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            DB::table('daily_shop_metrics')->updateOrInsert(
                ['date' => $dateStr, 'shop_id' => $shopId],
                [
                    'shop_page_views' => $shopPageViews,
                    'job_page_views' => $jobPageViews,
                    'apply_clicks' => $applyClicks,
                    'applications' => $applications,
                    'keeps' => $keeps,
                    'updated_at' => now(),
                ]
            );
        }
        
        $this->info("  {$shops->count()}店舗のメトリクスを集計しました");
    }
    
    /**
     * 求人ごとのメトリクスを集計
     */
    private function aggregateJobMetrics($dateStr, $startOfDay, $endOfDay)
    {
        $this->info("求人メトリクスを集計中...");
        
        // 求人一覧を取得
        $jobs = Job::select('id', 'shop_id')->get();
        
        foreach ($jobs as $job) {
            // 求人ページPV
            $pageViews = DB::table('event_logs')
                ->where('event_type', 'job_view')
                ->where('job_id', $job->id)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // 応募ボタンクリック数
            $applyClicks = DB::table('event_logs')
                ->where('event_type', 'apply_click')
                ->where('job_id', $job->id)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // 応募完了数
            $applications = DB::table('event_logs')
                ->where('event_type', 'apply_complete')
                ->where('job_id', $job->id)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            // キープ追加数（将来実装）
            $keeps = DB::table('event_logs')
                ->where('event_type', 'keep_add')
                ->where('job_id', $job->id)
                ->whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->count();
            
            DB::table('daily_job_metrics')->updateOrInsert(
                ['date' => $dateStr, 'job_id' => $job->id],
                [
                    'shop_id' => $job->shop_id,
                    'page_views' => $pageViews,
                    'apply_clicks' => $applyClicks,
                    'applications' => $applications,
                    'keeps' => $keeps,
                    'updated_at' => now(),
                ]
            );
        }
        
        $this->info("  {$jobs->count()}求人のメトリクスを集計しました");
    }
}
