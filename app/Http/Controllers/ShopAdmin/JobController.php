<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * 求人一覧を表示
     */
    public function index()
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $jobs = Job::where('shop_id', $shopId)
            ->withCount([
                'applications as application_count',
                'applications as pending_applications' => function ($query) {
                    $query->where('status', 'pending');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shop-admin.jobs.index', compact('jobs'));
    }

    /**
     * 求人作成フォームを表示
     */
    public function create()
    {
        return view('shop-admin.jobs.create');
    }

    /**
     * 求人を作成
     */
    public function store(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'job_type' => 'required|in:part_time,full_time,contract',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0|gte:salary_min',
            'work_hours' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'job_conditions' => 'nullable|array',
            'job_conditions.*' => 'string',
            'uniform_description' => 'nullable|string|max:1000',
            'uniform_images' => 'nullable|array|max:5',
            'uniform_images.*' => 'nullable|string|url|max:255',
            'gender_requirement' => 'required|in:male,female,any',
            'age_min' => 'nullable|integer|min:0',
            'age_max' => 'nullable|integer|min:0|gte:age_min',
            'status' => 'required|in:active,inactive,closed',
            'application_deadline' => 'nullable|date',
        ]);

        // 空の画像URLをフィルタリング
        if (isset($validated['uniform_images'])) {
            $validated['uniform_images'] = array_filter($validated['uniform_images'], function($url) {
                return !empty(trim($url));
            });
            if (empty($validated['uniform_images'])) {
                $validated['uniform_images'] = null;
            }
        }

        $validated['shop_id'] = $shopId;
        
        // trial_visit_availableをbooleanに変換
        $validated['trial_visit_available'] = $request->has('trial_visit_available') && $request->trial_visit_available == '1';
        
        // job_conditionsは配列のまま保存（Eloquentのキャストで自動的にJSONに変換される）
        Job::create($validated);

        return redirect()->route('shop-admin.jobs.index')
            ->with('success', '求人を投稿しました。');
    }

    /**
     * 求人詳細を表示
     */
    public function show($id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $job = Job::where('shop_id', $shopId)
            ->with(['shop', 'applications.user'])
            ->findOrFail($id);

        return view('shop-admin.jobs.show', compact('job'));
    }

    /**
     * 求人編集フォームを表示
     */
    public function edit($id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $job = Job::where('shop_id', $shopId)
            ->findOrFail($id);

        return view('shop-admin.jobs.edit', compact('job'));
    }

    /**
     * 求人を更新
     */
    public function update(Request $request, $id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $job = Job::where('shop_id', $shopId)
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'job_type' => 'required|in:part_time,full_time,contract',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0|gte:salary_min',
            'work_hours' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'job_conditions' => 'nullable|array',
            'job_conditions.*' => 'string',
            'uniform_description' => 'nullable|string|max:1000',
            'uniform_images' => 'nullable|array|max:5',
            'uniform_images.*' => 'nullable|string|url|max:255',
            'trial_visit_available' => 'nullable|boolean',
            'gender_requirement' => 'required|in:male,female,any',
            'age_min' => 'nullable|integer|min:0',
            'age_max' => 'nullable|integer|min:0|gte:age_min',
            'status' => 'required|in:active,inactive,closed',
            'application_deadline' => 'nullable|date',
        ]);

        // 空の画像URLをフィルタリング
        if (isset($validated['uniform_images'])) {
            $validated['uniform_images'] = array_filter($validated['uniform_images'], function($url) {
                return !empty(trim($url));
            });
            if (empty($validated['uniform_images'])) {
                $validated['uniform_images'] = null;
            }
        }

        // trial_visit_availableをbooleanに変換
        $validated['trial_visit_available'] = $request->has('trial_visit_available') && $request->trial_visit_available == '1';

        // job_conditionsは配列のまま保存（Eloquentのキャストで自動的にJSONに変換される）
        if (!isset($validated['job_conditions'])) {
            $validated['job_conditions'] = null;
        }

        $job->update($validated);

        return redirect()->route('shop-admin.jobs.index')
            ->with('success', '求人情報を更新しました。');
    }
}

