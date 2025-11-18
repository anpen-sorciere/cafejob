<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GenderMst;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $genders = GenderMst::whereIn('id', [1, 2])->orderBy('id')->get();
        return view('auth.register', compact('genders'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['required', 'date', 'before:' . now()->subYears(16)->format('Y-m-d')],
            'gender_id' => ['required', 'integer', 'in:1,2'],
        ], [
            'birth_date.required' => '生年月日を入力してください。',
            'birth_date.date' => '有効な日付を入力してください。',
            'birth_date.before' => '16歳以上である必要があります。',
            'gender_id.required' => '性別を選択してください。',
            'gender_id.in' => '性別は女性または男性を選択してください。',
        ]);

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password), // 直接password_hashを設定
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'birth_date' => $request->birth_date,
                'gender_id' => $request->gender_id,
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(RouteServiceProvider::HOME);
        } catch (\Exception $e) {
            \Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return back()->withInput()->withErrors([
                'registration' => '登録中にエラーが発生しました。もう一度お試しください。',
            ]);
        }
    }
}
