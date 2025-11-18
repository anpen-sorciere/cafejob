<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopAdmin;
use App\Models\Prefecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ShopRegisterController extends Controller
{
    /**
     * 店舗登録フォーム表示
     */
    public function create()
    {
        $prefectures = Prefecture::orderBy('id')->get();
        // registルートの場合はregist/createビューを使用
        if (request()->routeIs('regist.*')) {
            return view('regist.create', compact('prefectures'));
        }
        return view('shop-register.create', compact('prefectures'));
    }

    /**
     * 店舗登録処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'postal_code' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string'],
            'prefecture_id' => ['required', 'integer', 'exists:prefectures,id'],
            'city_name' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'opening_hours' => ['nullable', 'string'],
            'concept_type' => ['required', 'string'],
            'uniform_type' => ['nullable', 'string'],
            'admin_last_name' => ['required', 'string', 'max:50'],
            'admin_first_name' => ['required', 'string', 'max:50'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:shop_admins,email'],
            'admin_email_confirm' => ['required', 'email', 'same:admin_email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            // 確認コード生成
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // 郵便番号処理
            $postalCode = str_pad(str_replace('-', '', $request->postal_code), 7, '0', STR_PAD_LEFT);
            $fullAddress = $request->city_name . $request->address;

            // 店舗作成
            $shop = Shop::create([
                'name' => $request->name,
                'description' => $request->description,
                'postal_code' => $postalCode,
                'address' => $fullAddress,
                'prefecture_id' => $request->prefecture_id,
                'city_id' => null,
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'opening_hours' => $request->opening_hours,
                'concept_type' => $request->concept_type,
                'uniform_type' => $request->uniform_type,
                'status' => 'verification_pending',
                'verification_code' => $verificationCode,
                'verification_sent_at' => now(),
            ]);

            // 店舗管理者作成
            $adminUsername = $request->admin_last_name . $request->admin_first_name;
            ShopAdmin::create([
                'shop_id' => $shop->id,
                'username' => $adminUsername,
                'email' => $request->admin_email,
                'password_hash' => Hash::make($request->admin_password),
                'status' => 'active',
            ]);

            DB::commit();

            // registルートの場合はregistルートにリダイレクト
            $redirectRoute = request()->routeIs('regist.*') ? 'regist.create' : 'shop-admin.login';
            return redirect()->route($redirectRoute)
                ->with('success', '店舗登録が完了しました。住所確認のため、入力された住所に6桁の確認コード（' . $verificationCode . '）を記載した郵便を送信いたします。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', '登録中にエラーが発生しました: ' . $e->getMessage());
        }
    }
}

