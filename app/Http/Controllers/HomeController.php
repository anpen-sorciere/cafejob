<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\AccessLogger;

class HomeController extends Controller
{
    /**
     * ホームページ表示
     */
    public function index()
    {
        // 統計データ
        $stats = [
            'total_jobs' => Job::where('status', 'active')->count(),
            'total_shops' => Shop::where('status', 'active')->count(),
            'total_casts' => DB::table('casts')->where('status', 'active')->count(),
            'total_applications' => DB::table('applications')->count(),
        ];

        // 人気求人ランキング
        $popular_jobs = Job::with(['shop.prefecture', 'shop.city'])
            ->where('status', 'active')
            ->withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 人気店舗ランキング
        $popular_shops = Shop::with(['prefecture', 'city'])
            ->where('status', 'active')
            ->withCount(['jobs', 'reviews'])
            ->orderBy('jobs_count', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->limit(5)
            ->get();

        // 最新求人
        $latest_jobs = Job::with(['shop.prefecture', 'shop.city'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // 最新店舗
        $latest_shops = Shop::with(['prefecture', 'city'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // ページビューを記録
        AccessLogger::logPageView($request);
        
        return view('home', compact('stats', 'popular_jobs', 'popular_shops', 'latest_jobs', 'latest_shops'));
    }
}

