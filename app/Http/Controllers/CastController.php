<?php

namespace App\Http\Controllers;

use App\Models\Cast;
use App\Models\Shop;
use Illuminate\Http\Request;

class CastController extends Controller
{
    /**
     * キャスト一覧表示
     */
    public function index(Request $request)
    {
        $query = Cast::with(['shop.prefecture', 'shop.city'])
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            });

        // 検索条件
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('nickname', 'like', "%{$keyword}%")
                  ->orWhere('hobby', 'like', "%{$keyword}%")
                  ->orWhere('special_skill', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('shop')) {
            $query->where('shop_id', $request->shop);
        }

        if ($request->filled('age_min')) {
            $query->where('age', '>=', $request->age_min);
        }

        if ($request->filled('age_max')) {
            $query->where('age', '<=', $request->age_max);
        }

        if ($request->filled('blood_type')) {
            $query->where('blood_type', $request->blood_type);
        }

        // ソート
        $sort = $request->get('sort', 'created_at');
        switch ($sort) {
            case 'age':
                $query->orderBy('age', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'height':
                $query->orderBy('height', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $casts = $query->paginate(20);
        $shops = Shop::where('status', 'active')->orderBy('name')->get();

        return view('casts.index', compact('casts', 'shops'));
    }

    /**
     * キャスト詳細表示
     */
    public function show($id)
    {
        $cast = Cast::with(['shop.prefecture', 'shop.city'])
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            })
            ->findOrFail($id);

        // 同じ店舗の他のキャスト
        $relatedCasts = Cast::with('shop')
            ->where('shop_id', $cast->shop_id)
            ->where('id', '!=', $id)
            ->where('status', 'active')
            ->whereHas('shop', function($q) {
                $q->where('status', 'active');
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // 店舗の求人情報
        $shopJobs = $cast->shop->jobs()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('casts.show', compact('cast', 'relatedCasts', 'shopJobs'));
    }
}

