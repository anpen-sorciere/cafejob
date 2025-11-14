<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Prefecture;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShopInfoController extends Controller
{
    /**
     * Display shop information edit form.
     */
    public function edit()
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;
        
        if (!$shop) {
            return redirect()->route('shop-admin.dashboard')
                ->with('error', '店舗情報が見つかりませんでした。');
        }

        $prefectures = Prefecture::orderBy('name')->get();
        $cities = $shop->prefecture_id 
            ? City::where('prefecture_id', $shop->prefecture_id)->orderBy('name')->get()
            : collect();

        return view('shop-admin.shop-info.edit', [
            'shop' => $shop,
            'prefectures' => $prefectures,
            'cities' => $cities,
        ]);
    }

    /**
     * Update shop information.
     */
    public function update(Request $request)
    {
        $shopAdmin = Auth::guard('shop_admin')->user();
        $shop = $shopAdmin->shop;
        
        if (!$shop) {
            return redirect()->route('shop-admin.dashboard')
                ->with('error', '店舗情報が見つかりませんでした。');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'postal_code' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:200'],
            'prefecture_id' => ['required', 'integer', 'exists:prefectures,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:100'],
            'website' => ['nullable', 'url', 'max:200'],
            'opening_hours' => ['required', 'string'],
            'concept_type' => ['required', 'in:maid,butler,idol,cosplay,other'],
            'uniform_type' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'atmosphere_images' => ['nullable', 'array', 'max:10'],
            'atmosphere_images.*' => ['nullable', 'string', 'url', 'max:255'],
            'job_features' => ['nullable', 'string', 'max:2000'],
        ]);

        // 郵便番号を7桁の文字列として保存（先頭の0を保持）
        $postalCode = str_replace('-', '', $validated['postal_code']);
        $postalCode = str_pad($postalCode, 7, '0', STR_PAD_LEFT);

        // 店舗情報を更新
        $shop->name = $validated['name'];
        $shop->description = $validated['description'];
        $shop->postal_code = $postalCode;
        $shop->address = $validated['address'];
        $shop->prefecture_id = $validated['prefecture_id'];
        $shop->city_id = $validated['city_id'] ?? null;
        $shop->phone = $validated['phone'];
        $shop->email = $validated['email'];
        $shop->website = $validated['website'] ?? null;
        $shop->opening_hours = $validated['opening_hours'];
        $shop->concept_type = $validated['concept_type'];
        $shop->uniform_type = $validated['uniform_type'] ?? null;
        
        // お店の雰囲気画像を更新
        if (isset($validated['atmosphere_images'])) {
            $atmosphereImages = array_filter($validated['atmosphere_images'], function($url) {
                return !empty(trim($url));
            });
            $shop->atmosphere_images = !empty($atmosphereImages) ? $atmosphereImages : null;
        } else {
            $shop->atmosphere_images = null;
        }
        
        // アルバイトの特徴を更新
        $shop->job_features = $validated['job_features'] ?? null;

        // 画像アップロード
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                // 古い画像を削除
                if ($shop->image_url) {
                    Storage::disk('public')->delete($shop->image_url);
                }
                
                // 新しい画像を保存
                $path = $file->store('shops', 'public');
                $shop->image_url = $path;
            }
        }

        $shop->save();

        return redirect()->route('shop-admin.shop-info')
            ->with('success', '店舗情報を更新しました。');
    }
}

