<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CastManagementController extends Controller
{
    /**
     * キャスト一覧
     */
    public function index()
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;

        if (!$shop) {
            return redirect()->route('shop-admin.dashboard')
                ->with('error', '店舗情報が見つかりませんでした。');
        }

        $casts = Cast::where('shop_id', $shop->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shop-admin.cast-management.index', compact('casts', 'shop'));
    }

    /**
     * キャスト作成
     */
    public function store(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;

        if (!$shop) {
            return redirect()->back()->with('error', '店舗情報が見つかりませんでした。');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'nickname' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:18|max:50',
            'height' => 'nullable|integer|min:140|max:200',
            'blood_type' => 'nullable|in:A,B,O,AB',
            'hobby' => 'nullable|string|max:500',
            'special_skill' => 'nullable|string|max:500',
            'profile_image' => 'nullable|string|max:200',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['shop_id'] = $shop->id;
        Cast::create($validated);

        return redirect()->route('shop-admin.cast-management.index')
            ->with('success', 'キャストを追加しました。');
    }

    /**
     * キャスト更新
     */
    public function update(Request $request, $id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;

        if (!$shop) {
            return redirect()->back()->with('error', '店舗情報が見つかりませんでした。');
        }

        $cast = Cast::where('id', $id)
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'nickname' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:18|max:50',
            'height' => 'nullable|integer|min:140|max:200',
            'blood_type' => 'nullable|in:A,B,O,AB',
            'hobby' => 'nullable|string|max:500',
            'special_skill' => 'nullable|string|max:500',
            'profile_image' => 'nullable|string|max:200',
            'status' => 'required|in:active,inactive',
        ]);

        $cast->update($validated);

        return redirect()->route('shop-admin.cast-management.index')
            ->with('success', 'キャスト情報を更新しました。');
    }

    /**
     * キャスト削除
     */
    public function destroy($id)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;

        if (!$shop) {
            return redirect()->back()->with('error', '店舗情報が見つかりませんでした。');
        }

        $cast = Cast::where('id', $id)
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        $cast->delete();

        return redirect()->route('shop-admin.cast-management.index')
            ->with('success', 'キャストを削除しました。');
    }
}

