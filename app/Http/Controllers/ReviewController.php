<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * 口コミ投稿
     */
    public function store(Request $request, $shopId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'content' => 'required|string|max:1000',
        ]);

        $shop = Shop::findOrFail($shopId);

        // 既に口コミを投稿しているかチェック
        $existingReview = Review::where('shop_id', $shopId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'この店舗には既に口コミを投稿しています。');
        }

        $review = Review::create([
            'shop_id' => $shopId,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
            'comment' => $request->content, // 旧スキーマとの互換性
            'status' => 'pending', // 承認待ち
        ]);

        return redirect()->back()
            ->with('success', '口コミを投稿しました。承認後に表示されます。');
    }
}

