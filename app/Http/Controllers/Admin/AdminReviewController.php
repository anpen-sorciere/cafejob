<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * 口コミ一覧
     */
    public function index(Request $request)
    {
        $query = Review::with(['shop', 'user']);

        // フィルター
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('shop', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhereHas('user', function($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * 口コミ詳細
     */
    public function show($id)
    {
        $review = Review::with(['shop', 'user'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * 口コミ承認
     */
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->save();

        return redirect()->route('admin.reviews.index')
            ->with('success', '口コミを承認しました。');
    }

    /**
     * 口コミ却下
     */
    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'rejected';
        $review->save();

        return redirect()->route('admin.reviews.index')
            ->with('success', '口コミを却下しました。');
    }
}

