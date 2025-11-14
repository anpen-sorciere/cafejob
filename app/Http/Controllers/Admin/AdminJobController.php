<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * 求人一覧
     */
    public function index(Request $request)
    {
        $query = Job::with(['shop'])
            ->withCount('applications');

        // フィルター
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('shop', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $jobs = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * 求人詳細
     */
    public function show($id)
    {
        $job = Job::with(['shop', 'applications.user'])->findOrFail($id);
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * 求人ステータス更新
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $job = Job::findOrFail($id);
        $job->update(['status' => $request->status]);

        return back()->with('success', '求人ステータスを更新しました。');
    }
}

