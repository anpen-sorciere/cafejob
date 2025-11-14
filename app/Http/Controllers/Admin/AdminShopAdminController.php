<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminShopAdminController extends Controller
{
    /**
     * 店舗管理者作成フォーム表示
     */
    public function create()
    {
        $shops = Shop::orderBy('name')->get();
        $shopAdmins = ShopAdmin::with('shop')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.shop-admins.create', compact('shops', 'shopAdmins'));
    }

    /**
     * 店舗管理者作成処理
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('shop_admins', 'username')
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('shop_admins', 'email')
            ],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.regex' => 'ユーザー名は英数字とアンダースコアのみ使用できます。',
            'username.unique' => 'このユーザー名は既に使用されています。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
        ]);

        ShopAdmin::create([
            'shop_id' => $validated['shop_id'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        return redirect()->route('admin.shop-admins.create')
            ->with('success', '店舗管理者アカウントが正常に作成されました。');
    }
}

