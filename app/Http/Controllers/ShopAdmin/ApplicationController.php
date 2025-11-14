<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\UserReport;
use App\Models\UserApplicationBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    /**
     * 応募一覧を表示
     */
    public function index(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        // フィルタリング用のパラメータ
        $statusFilter = $request->get('status', 'all');
        $jobFilter = $request->get('job_id', 'all');
        $search = $request->get('search', '');

        // 応募一覧の取得（フィルタリング対応）
        $query = Application::whereHas('job', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })
        ->with(['job', 'user']);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($jobFilter !== 'all') {
            $query->where('job_id', $jobFilter);
        }

        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('job', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('applied_at', 'desc')->paginate(20);

        // 店舗の求人一覧（フィルター用）
        $shopJobs = Job::where('shop_id', $shopId)
            ->orderBy('title')
            ->get();

        return view('shop-admin.applications.index', compact('applications', 'shopJobs', 'statusFilter', 'jobFilter', 'search'));
    }

    /**
     * 応募詳細を表示
     */
    public function show($id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $application = Application::whereHas('job', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })
        ->with(['job', 'user'])
        ->findOrFail($id);

        // 既に報告済みかチェック
        $existingReport = UserReport::where('application_id', $id)
            ->where('shop_admin_id', $shopAdmin->id)
            ->first();

        return view('shop-admin.applications.show', compact('application', 'existingReport'));
    }

    /**
     * 応募ステータスを更新
     */
    public function updateStatus(Request $request, $id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $application = Application::whereHas('job', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,cancelled',
        ]);

        $application->status = $request->status;
        $application->save();

        return redirect()->route('shop-admin.applications.show', $id)
            ->with('success', '応募ステータスを更新しました。');
    }

    /**
     * 求職者を報告
     */
    public function report(Request $request, $id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $application = Application::whereHas('job', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })
        ->with(['user'])
        ->findOrFail($id);

        $validated = $request->validate([
            'report_type' => 'required|in:no_show,inappropriate_behavior,false_information,other',
            'report_message' => 'required|string|max:30',
        ]);

        // 既に報告済みかチェック
        $existingReport = UserReport::where('application_id', $id)
            ->where('shop_admin_id', $shopAdmin->id)
            ->first();

        if ($existingReport) {
            return redirect()->route('shop-admin.applications.show', $id)
                ->with('error', 'この応募については既に報告済みです。');
        }

        $report = UserReport::create([
            'application_id' => $id,
            'shop_admin_id' => $shopAdmin->id,
            'user_id' => $application->user_id,
            'report_type' => $validated['report_type'],
            'message' => $validated['report_message'],
            'status' => 'pending',
        ]);

        return redirect()->route('shop-admin.applications.show', $id)
            ->with('success', '求職者を報告しました。システム管理者が確認します。');
    }

    /**
     * 求職者を応募禁止にする
     */
    public function ban(Request $request, $id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shopId = $shopAdmin->shop_id;

        $application = Application::whereHas('job', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })
        ->with(['user', 'job'])
        ->findOrFail($id);

        // 既に禁止されているかチェック
        $existingBan = UserApplicationBan::where('user_id', $application->user_id)
            ->where('shop_id', $shopId)
            ->where('status', 'active')
            ->where('banned_until', '>', now())
            ->first();

        if ($existingBan) {
            return redirect()->route('shop-admin.applications.show', $id)
                ->with('error', 'この求職者は既に応募禁止期間中です。');
        }

        // 報告が存在するかチェック（任意）
        $report = UserReport::where('application_id', $id)
            ->where('shop_admin_id', $shopAdmin->id)
            ->first();

        // 半年後の日時を計算
        $bannedUntil = Carbon::now()->addMonths(6);

        UserApplicationBan::create([
            'user_id' => $application->user_id,
            'shop_id' => $shopId,
            'user_report_id' => $report ? $report->id : null,
            'reason' => $report ? $report->message : '店舗管理者による応募禁止',
            'banned_until' => $bannedUntil,
            'banned_by' => $shopAdmin->id,
            'status' => 'active',
        ]);

        return redirect()->route('shop-admin.applications.show', $id)
            ->with('success', 'この求職者を半年間（' . $bannedUntil->format('Y年m月d日') . 'まで）応募禁止にしました。');
    }
}

