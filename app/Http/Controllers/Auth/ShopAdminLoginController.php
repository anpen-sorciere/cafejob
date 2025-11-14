<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ShopAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ShopAdminLoginController extends Controller
{
    /**
     * Show the shop admin login form.
     */
    public function showLoginForm()
    {
        return view('auth.shop-admin-login');
    }

    /**
     * Handle an incoming shop admin authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $shopAdmin = ShopAdmin::where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (!$shopAdmin) {
            throw ValidationException::withMessages([
                'username' => ['認証情報が正しくありません。'],
            ]);
        }

        // 既存システムとの互換性: 平文パスワードとハッシュパスワードの両方に対応
        $passwordValid = false;
        if ($shopAdmin->password_hash === $request->password) {
            // 平文パスワード（既存データとの互換性）
            $passwordValid = true;
        } else {
            // ハッシュパスワード
            $passwordValid = Hash::check($request->password, $shopAdmin->password_hash);
        }

        if (!$passwordValid) {
            throw ValidationException::withMessages([
                'username' => ['認証情報が正しくありません。'],
            ]);
        }

        // 他のガードをログアウト（多重ログイン防止）
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        Auth::guard('shop_admin')->login($shopAdmin, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->route('shop-admin.dashboard');
    }

    /**
     * Log the shop admin out of the application.
     */
    public function logout(Request $request)
    {
        // すべてのガードをログアウト
        Auth::guard('admin')->logout();
        Auth::guard('shop_admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/shop-admin/login');
    }
}

