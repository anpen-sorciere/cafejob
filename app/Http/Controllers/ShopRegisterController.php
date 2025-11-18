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
        // 厳格なバリデーション
        $request->validate([
            // 店舗基本情報
            'name' => ['required', 'string', 'max:100', 'unique:shops,name'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'concept_type' => ['required', 'string', 'in:maid,butler,idol,cosplay,other'],
            'uniform_type' => ['nullable', 'string', 'max:50'],
            'opening_hours' => ['required', 'string', 'max:500'],
            
            // 住所情報（厳格な形式チェック）
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-?\d{4}$/'],
            'prefecture_id' => ['required', 'integer', 'exists:prefectures,id'],
            'city_name' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:200'],
            
            // 連絡先情報
            'phone' => ['required', 'string', 'regex:/^0\d{1,4}-\d{1,4}-\d{4}$|^0\d{9,10}$/', 'max:20'],
            'email' => ['required', 'email', 'max:100', 'unique:shops,email'],
            'website' => ['nullable', 'url', 'max:200', 'regex:/^https?:\/\//'],
            
            // 管理者情報
            'admin_last_name' => ['required', 'string', 'max:50', 'regex:/^[ァ-ヶー一-龠々]+$/u'],
            'admin_first_name' => ['required', 'string', 'max:50', 'regex:/^[ァ-ヶー一-龠々]+$/u'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:shop_admins,email', 'different:email'],
            'admin_email_confirm' => ['required', 'email', 'same:admin_email'],
            'admin_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'],
            'admin_phone' => ['required', 'string', 'regex:/^0\d{1,4}-\d{1,4}-\d{4}$|^0\d{9,10}$/', 'max:20'],
            
            // 代表者情報
            'representative_name' => ['required', 'string', 'max:100'],
            'representative_position' => ['required', 'string', 'max:50'],
            
            // 審査用情報
            'business_type' => ['required', 'string', 'in:individual,corporation'],
            'corporation_name' => ['nullable', 'required_if:business_type,corporation', 'string', 'max:200'],
            'corporation_number' => ['nullable', 'required_if:business_type,corporation', 'string', 'max:50'],
            
            // 利用規約・同意
            'terms' => ['required', 'accepted'],
        ], [
            'name.unique' => 'この店舗名は既に登録されています。',
            'name.max' => '店舗名は100文字以内で入力してください。',
            'description.min' => '店舗説明は20文字以上で入力してください。',
            'description.max' => '店舗説明は2000文字以内で入力してください。',
            'postal_code.regex' => '郵便番号は正しい形式で入力してください（例：123-4567）。',
            'phone.regex' => '電話番号は正しい形式で入力してください（例：03-1234-5678）。',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'admin_email.unique' => 'この管理者メールアドレスは既に登録されています。',
            'admin_email.different' => '管理者メールアドレスは店舗メールアドレスと異なるものを入力してください。',
            'admin_password.regex' => 'パスワードは英大文字、英小文字、数字を含む8文字以上で入力してください。',
            'admin_last_name.regex' => '管理者姓は全角カタカナまたは漢字で入力してください。',
            'admin_first_name.regex' => '管理者名は全角カタカナまたは漢字で入力してください。',
            'website.regex' => 'ウェブサイトURLはhttp://またはhttps://で始まる必要があります。',
            'terms.accepted' => '利用規約への同意が必要です。',
        ]);

        try {
            DB::beginTransaction();

            // 確認コード生成
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // 郵便番号処理
            $postalCode = str_pad(str_replace('-', '', $request->postal_code), 7, '0', STR_PAD_LEFT);
            $fullAddress = $request->city_name . $request->address;

            // 店舗作成（審査用情報も含む）
            $shopData = [
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
            ];
            
            // 審査用情報（JSONとして保存するか、別テーブルに保存）
            // 現在のスキーマに存在しない場合は、後でマイグレーションで追加
            // とりあえず、job_featuresカラムにJSONとして保存（一時的な対応）
            $verificationData = [
                'representative_name' => $request->representative_name,
                'representative_position' => $request->representative_position,
                'business_type' => $request->business_type,
                'corporation_name' => $request->corporation_name,
                'corporation_number' => $request->corporation_number,
            ];
            $shopData['job_features'] = json_encode($verificationData);
            
            $shop = Shop::create($shopData);

            // 店舗管理者作成
            $adminUsername = $request->admin_last_name . $request->admin_first_name;
            $adminData = [
                'shop_id' => $shop->id,
                'username' => $adminUsername,
                'email' => $request->admin_email,
                'password_hash' => Hash::make($request->admin_password),
                'status' => 'active',
            ];
            
            // 管理者電話番号（現在のスキーマに存在しない場合は、後でマイグレーションで追加）
            // とりあえず、usernameに含める（一時的な対応）
            // 実際には、shop_adminsテーブルにphoneカラムを追加する必要がある
            ShopAdmin::create($adminData);
            
            // 管理者電話番号をログに記録（後でマイグレーションで追加するまで）
            \Log::info('Shop registration - Admin phone', [
                'shop_id' => $shop->id,
                'admin_phone' => $request->admin_phone,
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

