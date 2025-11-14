<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Prefecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * 店舗一覧表示
     */
    public function index(Request $request)
    {
        $query = Shop::with(['prefecture', 'city'])
            ->where('status', 'active')
            ->withCount(['jobs' => function($q) {
                $q->where('status', 'active');
            }, 'reviews' => function($q) {
                $q->where('status', 'approved');
            }])
            ->withAvg(['reviews' => function($q) {
                $q->where('status', 'approved');
            }], 'rating');

        // 検索条件
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('prefecture')) {
            $query->where('prefecture_id', $request->prefecture);
        }

        if ($request->filled('concept_type')) {
            $query->where('concept_type', $request->concept_type);
        }

        if ($request->filled('uniform_type')) {
            $query->where('uniform_type', 'like', "%{$request->uniform_type}%");
        }

        // ソート
        $sort = $request->get('sort', 'created_at');
        switch ($sort) {
            case 'rating':
                $query->orderBy('reviews_avg_rating', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('jobs_count', 'desc')
                      ->orderBy('reviews_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $shops = $query->paginate(20);
        $prefectures = Prefecture::orderBy('id')->get();

        // キープ状態の取得
        $keptShopIds = [];
        if (config('feature_flags.keep', false) && Auth::check()) {
            $keptShopIds = Auth::user()->favorites()
                ->whereNotNull('shop_id')
                ->pluck('shop_id')
                ->toArray();
        }

        return view('shops.index', compact('shops', 'prefectures', 'keptShopIds'));
    }

    /**
     * 店舗詳細表示
     */
    public function show($id)
    {
        $shop = Shop::with(['prefecture', 'city', 'casts', 'reviews' => function($q) {
            $q->where('status', 'approved')
              ->with('user')
              ->orderBy('created_at', 'desc');
        }])
            ->withAvg(['reviews' => function($q) {
                $q->where('status', 'approved');
            }], 'rating')
            ->where('status', 'active')
            ->findOrFail($id);

        // 関連求人
        $jobs = $shop->jobs()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // キープ状態
        $isKept = false;
        if (config('feature_flags.keep', false) && Auth::check()) {
            $isKept = Auth::user()->favorites()
                ->where('shop_id', $id)
                ->exists();
        }

        return view('shops.show', compact('shop', 'jobs', 'isKept'));
    }
}

