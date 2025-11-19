<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Shop;
use App\Models\Prefecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\AccessLogger;

class JobController extends Controller
{
    /**
     * 求人一覧表示
     */
    public function index(Request $request)
    {
        $query = Job::with(['shop.prefecture', 'shop.city'])
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            });

        // 検索条件
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhereHas('shop', function($q) use ($keyword) {
                      $q->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($request->filled('prefecture')) {
            $query->whereHas('shop', function($q) use ($request) {
                $q->where('prefecture_id', $request->prefecture);
            });
        }

        if ($request->filled('concept_type')) {
            $query->whereHas('shop', function($q) use ($request) {
                $q->where('concept_type', $request->concept_type);
            });
        }

        if ($request->filled('salary_min')) {
            $query->where('salary_min', '>=', $request->salary_min);
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->filled('gender_requirement')) {
            $query->where(function($q) use ($request) {
                $q->where('gender_requirement', $request->gender_requirement)
                  ->orWhere('gender_requirement', 'any');
            });
        }

        // ソート
        $sort = $request->get('sort', 'created_at');
        switch ($sort) {
            case 'salary':
                $query->orderBy('salary_min', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->withCount('applications')
                      ->orderBy('applications_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'deadline':
                $query->orderBy('application_deadline', 'asc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $jobs = $query->paginate(20);
        $prefectures = Prefecture::orderBy('id')->get();

        // キープ状態の取得
        $keptJobIds = [];
        if (config('feature_flags.keep', false) && Auth::check()) {
            $keptJobIds = Auth::user()->favorites()
                ->whereNotNull('job_id')
                ->pluck('job_id')
                ->toArray();
        }

        // ページビューを記録
        AccessLogger::logPageView($request);

        return view('jobs.index', compact('jobs', 'prefectures', 'keptJobIds'));
    }

    /**
     * 求人詳細表示
     */
    public function show($id)
    {
        $job = Job::with(['shop.prefecture', 'shop.city', 'shop.casts'])
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            })
            ->findOrFail($id);

        // 関連求人
        $relatedJobs = Job::with(['shop.prefecture', 'shop.city'])
            ->where('shop_id', $job->shop_id)
            ->where('id', '!=', $id)
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            })
            ->limit(3)
            ->get();

        // キャスト情報
        $casts = $job->shop->casts()->where('status', 'active')->get();

        // キープ状態
        $isKept = false;
        if (config('feature_flags.keep', false) && Auth::check()) {
            $isKept = Auth::user()->favorites()
                ->where('job_id', $id)
                ->exists();
        }

        // 求人詳細ページビューを記録
        AccessLogger::logJobView(request(), $job->id, $job->shop_id);

        return view('jobs.show', compact('job', 'relatedJobs', 'casts', 'isKept'));
    }
}

