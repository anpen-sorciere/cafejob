<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Job;
use App\Models\Cast;
use App\Models\User;
use App\Models\ShopAdmin;
use App\Models\Prefecture;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSampleDataController extends Controller
{
    /**
     * サンプルデータ生成フォーム表示
     */
    public function index()
    {
        return view('admin.sample-data.index');
    }

    /**
     * サンプルデータ生成処理
     */
    public function store(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'insert_sample_data') {
            $this->insertSampleData();
            return redirect()->route('admin.sample-data.index')
                ->with('success', 'サンプルデータの投入が完了しました。');
        } elseif ($action === 'clear_all_data') {
            $this->clearAllData();
            return redirect()->route('admin.sample-data.index')
                ->with('success', 'すべてのデータを削除しました。');
        }

        return redirect()->route('admin.sample-data.index')
            ->with('error', '無効なアクションです。');
    }

    /**
     * サンプルデータ投入
     */
    private function insertSampleData()
    {
        DB::beginTransaction();
        try {
            // 都道府県と市区町村データの確認
            if (Prefecture::count() === 0) {
                // 都道府県データを投入（簡易版）
                $prefectures = [
                    ['id' => 1, 'name' => '北海道'],
                    ['id' => 13, 'name' => '東京都'],
                    ['id' => 27, 'name' => '大阪府'],
                ];
                foreach ($prefectures as $pref) {
                    Prefecture::firstOrCreate(['id' => $pref['id']], ['name' => $pref['name']]);
                }
            }

            // サンプル店舗データ
            $sampleShops = [
                [
                    'name' => 'メイドカフェ・ミアカフェ秋葉原店',
                    'description' => '2004年創業の老舗メイドカフェ。秋葉原の中心地で、お客様をおもてなししています。',
                    'address' => '東京都千代田区外神田1-2-3',
                    'prefecture_id' => 13,
                    'city_id' => null,
                    'phone' => '03-1234-5678',
                    'email' => 'info@miacafe.com',
                    'website' => 'https://miacafe.com',
                    'opening_hours' => "平日 11:00-22:00\n土日祝 10:00-23:00",
                    'concept_type' => 'maid',
                    'uniform_type' => 'メイド服',
                    'status' => 'active'
                ],
                [
                    'name' => 'コンカフェ And Lovely',
                    'description' => '秋葉原で人気No.1のコンカフェ。可愛いキャストがお客様をお迎えします。',
                    'address' => '東京都千代田区外神田2-3-4',
                    'prefecture_id' => 13,
                    'city_id' => null,
                    'phone' => '03-2345-6789',
                    'email' => 'info@andlovely.com',
                    'website' => 'https://andlovely.com',
                    'opening_hours' => "平日 12:00-23:00\n土日祝 11:00-24:00",
                    'concept_type' => 'maid',
                    'uniform_type' => 'ロリータ服',
                    'status' => 'active'
                ],
                [
                    'name' => '執事喫茶 黒執事',
                    'description' => '上品な執事がおもてなしする執事喫茶。落ち着いた雰囲気でお楽しみいただけます。',
                    'address' => '東京都渋谷区道玄坂1-2-3',
                    'prefecture_id' => 13,
                    'city_id' => null,
                    'phone' => '03-3456-7890',
                    'email' => 'info@kuroshitsuji.com',
                    'website' => 'https://kuroshitsuji.com',
                    'opening_hours' => "平日 14:00-22:00\n土日祝 12:00-23:00",
                    'concept_type' => 'butler',
                    'uniform_type' => '執事服',
                    'status' => 'active'
                ],
            ];

            $createdShops = [];
            foreach ($sampleShops as $shopData) {
                $shop = Shop::create($shopData);
                $createdShops[] = $shop;
            }

            // サンプル求人データ
            if (!empty($createdShops)) {
                $sampleJobs = [
                    [
                        'shop_id' => $createdShops[0]->id,
                        'title' => 'メイドカフェスタッフ募集',
                        'description' => 'お客様をおもてなしするメイドスタッフを募集しています。未経験者も大歓迎！',
                        'job_type' => 'part_time',
                        'salary_min' => 1000,
                        'salary_max' => 1200,
                        'work_hours' => "平日 11:00-19:00\n土日祝 10:00-20:00",
                        'requirements' => '18歳以上、明るい性格、接客が好きな方',
                        'benefits' => '交通費支給、制服貸与、研修制度完備',
                        'gender_requirement' => 'female',
                        'age_min' => 18,
                        'age_max' => 30,
                        'status' => 'active',
                    ],
                ];

                foreach ($sampleJobs as $jobData) {
                    Job::create($jobData);
                }

                // サンプルキャストデータ
                $castData = [
                    [
                        'shop_id' => $createdShops[0]->id,
                        'name' => 'みお',
                        'nickname' => 'みおちゃん',
                        'age' => 22,
                        'height' => 158,
                        'blood_type' => 'A',
                        'hobby' => '読書、映画鑑賞',
                        'special_skill' => 'お茶の淹れ方',
                        'status' => 'active',
                    ],
                ];

                foreach ($castData as $cast) {
                    Cast::create($cast);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 全データ削除
     */
    private function clearAllData()
    {
        DB::beginTransaction();
        try {
            // 外部キー制約を考慮して順番に削除
            DB::table('chat_notifications')->delete();
            DB::table('chat_messages')->delete();
            DB::table('chat_rooms')->delete();
            DB::table('applications')->delete();
            DB::table('favorites')->delete();
            DB::table('reviews')->delete();
            DB::table('jobs')->delete();
            DB::table('casts')->delete();
            DB::table('shop_admins')->delete();
            DB::table('shops')->delete();
            DB::table('users')->where('id', '>', 1)->delete(); // 最初のユーザーは残す

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

