<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * ユーザー一覧
     */
    public function index(Request $request)
    {
        $query = User::withCount('applications');

        // フィルター
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * ユーザー詳細
     */
    public function show($id)
    {
        $user = User::with(['applications.job.shop'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * ユーザーステータス更新
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);

        return back()->with('success', 'ユーザーステータスを更新しました。');
    }
}

