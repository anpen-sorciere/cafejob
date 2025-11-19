<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MetricsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:shop_admin');
    }

    /**
     * 月次応募数（棒）
     */
    public function monthlyApplications(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $months = $request->get('months', 6);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subMonths($months - 1)->startOfMonth();

        $data = DB::table('daily_shop_metrics')
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(applications) as total_applications')
            )
            ->where('shop_id', $shopId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'labels' => $data->pluck('month')->map(fn($month) => Carbon::parse($month . '-01')->format('Y年m月')),
            'applications' => $data->pluck('total_applications'),
        ]);
    }

    /**
     * 月次PV（折れ線）
     */
    public function monthlyPageViews(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $months = $request->get('months', 6);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subMonths($months - 1)->startOfMonth();

        $data = DB::table('daily_shop_metrics')
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(shop_page_views + job_page_views) as total_page_views')
            )
            ->where('shop_id', $shopId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'labels' => $data->pluck('month')->map(fn($month) => Carbon::parse($month . '-01')->format('Y年m月')),
            'pageViews' => $data->pluck('total_page_views'),
        ]);
    }

    /**
     * 求人別応募数（棒）
     */
    public function jobApplications(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days);

        $data = DB::table('daily_job_metrics')
            ->join('jobs', 'daily_job_metrics.job_id', '=', 'jobs.id')
            ->select(
                'jobs.id',
                'jobs.title',
                DB::raw('SUM(daily_job_metrics.applications) as total_applications')
            )
            ->where('daily_job_metrics.shop_id', $shopId)
            ->whereBetween('daily_job_metrics.date', [$startDate, $endDate])
            ->groupBy('jobs.id', 'jobs.title')
            ->orderBy('total_applications', 'desc')
            ->get();

        return response()->json([
            'labels' => $data->pluck('title')->map(fn($title) => mb_strlen($title) > 15 ? mb_substr($title, 0, 15) . '...' : $title),
            'applications' => $data->pluck('total_applications'),
        ]);
    }

    /**
     * 求人別PV（棒）
     */
    public function jobPageViews(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days);

        $data = DB::table('daily_job_metrics')
            ->join('jobs', 'daily_job_metrics.job_id', '=', 'jobs.id')
            ->select(
                'jobs.id',
                'jobs.title',
                DB::raw('SUM(daily_job_metrics.page_views) as total_page_views')
            )
            ->where('daily_job_metrics.shop_id', $shopId)
            ->whereBetween('daily_job_metrics.date', [$startDate, $endDate])
            ->groupBy('jobs.id', 'jobs.title')
            ->orderBy('total_page_views', 'desc')
            ->get();

        return response()->json([
            'labels' => $data->pluck('title')->map(fn($title) => mb_strlen($title) > 15 ? mb_substr($title, 0, 15) . '...' : $title),
            'pageViews' => $data->pluck('total_page_views'),
        ]);
    }

    /**
     * 求人別応募率（散布図）
     */
    public function jobApplicationRate(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $days = $request->get('days', 30);
        $endDate = Carbon::today('Asia/Tokyo');
        $startDate = $endDate->copy()->subDays($days);

        $data = DB::table('daily_job_metrics')
            ->join('jobs', 'daily_job_metrics.job_id', '=', 'jobs.id')
            ->select(
                'jobs.id',
                'jobs.title',
                DB::raw('SUM(daily_job_metrics.page_views) as total_page_views'),
                DB::raw('SUM(daily_job_metrics.applications) as total_applications')
            )
            ->where('daily_job_metrics.shop_id', $shopId)
            ->whereBetween('daily_job_metrics.date', [$startDate, $endDate])
            ->groupBy('jobs.id', 'jobs.title')
            ->havingRaw('SUM(daily_job_metrics.page_views) > 0')
            ->get();

        $points = $data->map(function($item) {
            $rate = $item->total_page_views > 0 
                ? round(($item->total_applications / $item->total_page_views) * 100, 2)
                : 0;
            return [
                'x' => $item->total_page_views,
                'y' => $rate,
                'label' => mb_strlen($item->title) > 15 ? mb_substr($item->title, 0, 15) . '...' : $item->title,
            ];
        });

        return response()->json([
            'data' => $points,
        ]);
    }
}
