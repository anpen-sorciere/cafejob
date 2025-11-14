<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Shop;
use App\Models\Application;
use App\Models\Cast;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UpdateController extends Controller
{
    /**
     * 最新情報表示
     */
    public function index()
    {
        // 最新の求人情報（30分以内）
        $recentJobs = Job::with(['shop.prefecture', 'shop.city'])
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            })
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // 最新の店舗情報（1時間以内）
        $recentShops = Shop::with(['prefecture', 'city'])
            ->where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 最新の応募情報（1時間以内）
        $recentApplications = Application::with(['job.shop', 'user'])
            ->where('applied_at', '>=', Carbon::now()->subHour())
            ->orderBy('applied_at', 'desc')
            ->limit(10)
            ->get();

        // 最新のキャスト情報（2時間以内）
        $recentCasts = Cast::with('shop')
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            })
            ->where('created_at', '>=', Carbon::now()->subHours(2))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('updates.index', compact('recentJobs', 'recentShops', 'recentApplications', 'recentCasts'));
    }
}

