<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationAttempt;
use App\Models\Shop;
use Illuminate\Http\Request;

class AdminVerificationAttemptController extends Controller
{
    /**
     * 検証試行履歴一覧
     */
    public function index(Request $request)
    {
        $query = VerificationAttempt::with('shop');

        // フィルター
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->filled('attempt_type')) {
            $query->where('attempt_type', $request->attempt_type);
        }

        if ($request->filled('is_successful')) {
            $query->where('is_successful', $request->is_successful);
        }

        $attempts = $query->orderBy('attempt_time', 'desc')->paginate(20);
        $shops = Shop::orderBy('name')->get();

        return view('admin.verification-attempts.index', compact('attempts', 'shops'));
    }
}

