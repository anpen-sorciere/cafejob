<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:shop_admin');
    }

    /**
     * 店舗管理者ダッシュボード
     */
    public function index()
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;

        $stats = [
            'total_jobs' => Job::where('shop_id', $shop->id)->count(),
            'active_jobs' => Job::where('shop_id', $shop->id)->where('status', 'active')->count(),
            'total_applications' => Application::whereHas('job', function($q) use ($shop) {
                $q->where('shop_id', $shop->id);
            })->count(),
            'pending_applications' => Application::whereHas('job', function($q) use ($shop) {
                $q->where('shop_id', $shop->id);
            })->where('status', 'pending')->count(),
        ];

        $recentApplications = Application::with(['job', 'user'])
            ->whereHas('job', function($q) use ($shop) {
                $q->where('shop_id', $shop->id);
            })
            ->orderBy('applied_at', 'desc')
            ->limit(10)
            ->get();

        $shopJobs = Job::where('shop_id', $shop->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shop-admin.dashboard', compact('shop', 'stats', 'recentApplications', 'shopJobs'));
    }
}

