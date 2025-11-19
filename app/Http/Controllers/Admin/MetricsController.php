<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MetricsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * PV/UU/応募数（直近30日）
     */
    public function pvUuApplications(Request $request)
    {
        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days - 1);

        $data = DB::table('daily_site_metrics')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('m/d')),
            'pageViews' => $data->pluck('page_views'),
            'uniqueUsers' => $data->pluck('unique_users'),
            'applications' => $data->pluck('applications'),
        ]);
    }

    /**
     * 新規求職者登録数（直近30日）
     */
    public function newUsers(Request $request)
    {
        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days - 1);

        $data = DB::table('daily_site_metrics')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('m/d')),
            'newUsers' => $data->pluck('new_users'),
        ]);
    }

    /**
     * 新規掲載店舗数（直近30日）
     */
    public function newShops(Request $request)
    {
        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days - 1);

        $data = DB::table('daily_site_metrics')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('m/d')),
            'newShops' => $data->pluck('new_shops'),
        ]);
    }

    /**
     * 1店舗あたり平均応募数（折れ線）
     */
    public function averageApplicationsPerShop(Request $request)
    {
        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days - 1);

        $siteMetrics = DB::table('daily_site_metrics')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        // 開始日時点の店舗数を取得
        $initialShopCount = DB::table('shops')
            ->where('created_at', '<', $startDate->startOfDay())
            ->count();

        // 各日の新規店舗数を取得
        $newShopsByDate = DB::table('shops')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // 累積店舗数を計算
        $cumulativeShops = [];
        $currentShops = $initialShopCount;
        foreach ($siteMetrics as $metric) {
            $date = $metric->date;
            if (isset($newShopsByDate[$date])) {
                $currentShops += $newShopsByDate[$date]->count;
            }
            $cumulativeShops[$date] = $currentShops;
        }

        // 平均応募数を計算
        $averages = $siteMetrics->map(function($metric) use ($cumulativeShops) {
            $shopCount = $cumulativeShops[$metric->date] ?? 1;
            return $shopCount > 0 ? round($metric->applications / $shopCount, 2) : 0;
        });

        return response()->json([
            'labels' => $siteMetrics->pluck('date')->map(fn($date) => Carbon::parse($date)->format('m/d')),
            'averageApplications' => $averages,
        ]);
    }

    /**
     * 求人別応募数ランキング（棒）
     */
    public function jobApplicationRanking(Request $request)
    {
        $limit = $request->get('limit', 10);
        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days);

        $ranking = DB::table('daily_job_metrics')
            ->join('jobs', 'daily_job_metrics.job_id', '=', 'jobs.id')
            ->select(
                'jobs.id',
                'jobs.title',
                DB::raw('SUM(daily_job_metrics.applications) as total_applications')
            )
            ->whereBetween('daily_job_metrics.date', [$startDate, $endDate])
            ->groupBy('jobs.id', 'jobs.title')
            ->orderBy('total_applications', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'labels' => $ranking->pluck('title')->map(fn($title) => mb_strlen($title) > 20 ? mb_substr($title, 0, 20) . '...' : $title),
            'applications' => $ranking->pluck('total_applications'),
        ]);
    }
}
