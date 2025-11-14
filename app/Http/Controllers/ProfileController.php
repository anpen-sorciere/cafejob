<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update profile images only.
     */
    public function updateImages(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'profile_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB
            'profile_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB
        ]);

        // プロフィール画像1のアップロード
        if ($request->hasFile('profile_image_1')) {
            $file = $request->file('profile_image_1');
            if ($file->isValid()) {
                // 古い画像を削除
                if ($user->profile_image_1) {
                    Storage::disk('public')->delete($user->profile_image_1);
                }
                
                // 新しい画像を保存
                $path = $file->store('profiles', 'public');
                $user->profile_image_1 = $path;
            }
        }

        // プロフィール画像2のアップロード
        if ($request->hasFile('profile_image_2')) {
            $file = $request->file('profile_image_2');
            if ($file->isValid()) {
                // 古い画像を削除
                if ($user->profile_image_2) {
                    Storage::disk('public')->delete($user->profile_image_2);
                }
                
                // 新しい画像を保存
                $path = $file->store('profiles', 'public');
                $user->profile_image_2 = $path;
            }
        }

        // 画像削除リクエスト
        if ($request->has('delete_profile_image_1')) {
            if ($user->profile_image_1) {
                Storage::disk('public')->delete($user->profile_image_1);
                $user->profile_image_1 = null;
            }
        }

        if ($request->has('delete_profile_image_2')) {
            if ($user->profile_image_2) {
                Storage::disk('public')->delete($user->profile_image_2);
                $user->profile_image_2 = null;
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-images-updated');
    }
}
