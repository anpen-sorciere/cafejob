<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming admin authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (!$admin) {
            throw ValidationException::withMessages([
                'username' => ['認証情報が正しくありません。'],
            ]);
        }

        // 既存システムとの互換性: 平文パスワードとハッシュパスワードの両方に対応
        $passwordValid = false;
        if ($admin->password_hash === $request->password) {
            // 平文パスワード（既存データとの互換性）
            $passwordValid = true;
        } else {
            // ハッシュパスワード
            $passwordValid = Hash::check($request->password, $admin->password_hash);
        }

        if (!$passwordValid) {
            throw ValidationException::withMessages([
                'username' => ['認証情報が正しくありません。'],
            ]);
        }

        // 他のガードをログアウト（多重ログイン防止）
        Auth::guard('web')->logout();
        Auth::guard('shop_admin')->logout();

        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Log the admin out of the application.
     */
    public function logout(Request $request)
    {
        // すべてのガードをログアウト
        Auth::guard('admin')->logout();
        Auth::guard('shop_admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}

