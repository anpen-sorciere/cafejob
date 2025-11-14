<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Job;
use App\Models\Application;
use App\Models\Cast;
use App\Models\Review;
use App\Models\UserReport;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * 管理者ダッシュボード
     */
    public function index()
    {
        $stats = [
            'total_users' => User::where('status', 'active')->count(),
            'total_shops' => Shop::count(),
            'total_jobs' => Job::count(),
            'total_applications' => Application::count(),
            'total_casts' => Cast::count(),
            'pending_shops' => Shop::whereIn('status', ['pending', 'verification_pending'])->count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
            'pending_reports' => UserReport::where('status', 'pending')->count(),
        ];

        $recentApplications = Application::with(['job.shop', 'user'])
            ->orderBy('applied_at', 'desc')
            ->limit(10)
            ->get();

        $recentShops = Shop::with(['prefecture', 'city'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentReviews = Review::with(['shop', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'recentShops', 'recentReviews'));
    }
}

