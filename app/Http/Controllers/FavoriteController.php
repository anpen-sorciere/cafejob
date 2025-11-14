<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * キープ一覧表示
     */
    public function index()
    {
        if (!config('feature_flags.keep', false)) {
            return redirect()->route('home');
        }

        $keptJobs = Favorite::with([
            'job.shop.prefecture',
            'job.shop.city'
        ])
            ->where('user_id', Auth::id())
            ->whereNotNull('job_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('favorites.index', compact('keptJobs'));
    }

    /**
     * 一括応募処理
     */
    public function bulkApply(Request $request)
    {
        if (!config('feature_flags.keep', false)) {
            return back()->with('error', '機能が無効化されています。');
        }

        $request->validate([
            'job_ids' => ['required', 'array'],
            'job_ids.*' => ['integer', 'exists:jobs,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $selectedJobIds = $request->job_ids;
        $bulkMessage = $request->message ?? '';
        $results = [];

        if (empty($selectedJobIds)) {
            return back()->with('error', '応募する求人を選択してください。');
        }

        foreach ($selectedJobIds as $jobId) {
            // キープされているか確認
            $favorite = Favorite::where('user_id', Auth::id())
                ->where('job_id', $jobId)
                ->first();

            if (!$favorite) {
                $results[] = [
                    'status' => 'warning',
                    'message' => 'キープしていない求人が含まれていたためスキップしました。',
                ];
                continue;
            }

            // 求人の存在確認
            $job = Job::find($jobId);
            if (!$job) {
                $results[] = [
                    'status' => 'warning',
                    'message' => '求人が見つからなかったためスキップしました。',
                ];
                continue;
            }

            // 募集停止中か確認
            if ($job->status !== 'active') {
                $results[] = [
                    'status' => 'warning',
                    'message' => $job->title . ' は募集が停止中のため応募できませんでした。',
                ];
                continue;
            }

            // 既に応募済みか確認
            $existing = Application::where('user_id', Auth::id())
                ->where('job_id', $jobId)
                ->first();

            if ($existing) {
                $results[] = [
                    'status' => 'info',
                    'message' => $job->title . ' には既に応募済みです。',
                ];
                continue;
            }

            // 応募データを作成
            Application::create([
                'job_id' => $jobId,
                'user_id' => Auth::id(),
                'message' => $bulkMessage,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            $results[] = [
                'status' => 'success',
                'message' => $job->title . ' に応募が送信されました。',
            ];
        }

        return back()->with('bulk_results', $results);
    }
}
