<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * 応募一覧
     */
    public function index(Request $request)
    {
        $query = Application::with(['job.shop', 'user']);

        // フィルター
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('job', function($jq) use ($search) {
                    $jq->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('job.shop', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%");
                });
            });
        }

        $applications = $query->orderBy('applied_at', 'desc')->paginate(20);

        return view('admin.applications.index', compact('applications'));
    }

    /**
     * 応募詳細
     */
    public function show($id)
    {
        $application = Application::with(['job.shop', 'user'])->findOrFail($id);
        return view('admin.applications.show', compact('application'));
    }

    /**
     * 応募ステータス更新
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:pending,accepted,rejected,cancelled'],
        ]);

        $application = Application::findOrFail($id);
        $application->update(['status' => $request->status]);

        return back()->with('success', '応募ステータスを更新しました。');
    }
}

