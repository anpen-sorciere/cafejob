<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * 求職者報告一覧
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'all');
        $reportTypeFilter = $request->get('report_type', 'all');
        $search = $request->get('search', '');

        $query = UserReport::with(['application.job.shop', 'shopAdmin', 'user', 'reviewedBy']);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($reportTypeFilter !== 'all') {
            $query->where('report_type', $reportTypeFilter);
        }

        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('shopAdmin', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        $reportTypeLabels = [
            'no_show' => '面接ドタキャン',
            'inappropriate_behavior' => '不適切な行動',
            'false_information' => '虚偽の情報',
            'other' => 'その他',
        ];

        $statusLabels = [
            'pending' => '未確認',
            'reviewed' => '確認済み',
            'resolved' => '対応済み',
            'dismissed' => '却下',
        ];

        return view('admin.user-reports.index', compact(
            'reports',
            'statusFilter',
            'reportTypeFilter',
            'search',
            'reportTypeLabels',
            'statusLabels'
        ));
    }

    /**
     * 求職者報告詳細
     */
    public function show($id)
    {
        $report = UserReport::with([
            'application.job.shop',
            'shopAdmin.shop',
            'user',
            'reviewedBy'
        ])->findOrFail($id);

        $reportTypeLabels = [
            'no_show' => '面接ドタキャン',
            'inappropriate_behavior' => '不適切な行動',
            'false_information' => '虚偽の情報',
            'other' => 'その他',
        ];

        $statusLabels = [
            'pending' => '未確認',
            'reviewed' => '確認済み',
            'resolved' => '対応済み',
            'dismissed' => '却下',
        ];

        return view('admin.user-reports.show', compact('report', 'reportTypeLabels', 'statusLabels'));
    }

    /**
     * 報告ステータスを更新
     */
    public function updateStatus(Request $request, $id)
    {
        $report = UserReport::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $report->status = $validated['status'];
        $report->admin_notes = $validated['admin_notes'] ?? null;
        $report->reviewed_by = Auth::guard('admin')->id();
        $report->reviewed_at = now();
        $report->save();

        return redirect()->route('admin.user-reports.show', $id)
            ->with('success', '報告ステータスを更新しました。');
    }
}

