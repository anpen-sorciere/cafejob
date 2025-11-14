<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class AdminShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * 店舗一覧
     */
    public function index(Request $request)
    {
        $query = Shop::with(['prefecture', 'city'])
            ->withCount('jobs');

        // フィルター
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $shops = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.shops.index', compact('shops'));
    }

    /**
     * 店舗詳細
     */
    public function show($id)
    {
        $shop = Shop::with(['prefecture', 'city', 'jobs', 'casts'])->findOrFail($id);
        return view('admin.shops.show', compact('shop'));
    }

    /**
     * 店舗ステータス更新
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,pending,verification_pending,inactive'],
        ]);

        $shop = Shop::findOrFail($id);
        $shop->update(['status' => $request->status]);

        return back()->with('success', '店舗ステータスを更新しました。');
    }
}

