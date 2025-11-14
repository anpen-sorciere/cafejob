<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prefecture;
use App\Models\City;
use App\Models\Shop;
use App\Models\Job;
use App\Models\User;
use App\Models\ShopAdmin;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 都道府県と市区町村は既に存在する前提
        
        // テストユーザー（既に存在する場合はスキップ）
        $testUser = User::firstOrCreate(
            ['username' => 'test_user'],
            [
                'email' => 'test@example.com',
                'password' => Hash::make('test123'),
                'first_name' => 'テスト',
                'last_name' => 'ユーザー',
                'status' => 'active',
            ]
        );

        // 都道府県を取得（東京都=13, 大阪府=27）
        $tokyo = Prefecture::where('name', '東京都')->first();
        $osaka = Prefecture::where('name', '大阪府')->first();
        
        if (!$tokyo || !$osaka) {
            $this->command->error('都道府県データが見つかりません。先にPrefectureSeederを実行してください。');
            return;
        }

        // テスト店舗1: メイドカフェ
        $shop1 = Shop::firstOrCreate(
            ['name' => 'メイドカフェ サクラ'],
            [
                'prefecture_id' => $tokyo->id,
                'city_id' => null, // 市区町村は任意
                'address' => '秋葉原1-1-1',
                'phone' => '03-1234-5678',
                'email' => 'shop1@example.com',
                'concept_type' => 'maid',
                'uniform_type' => 'maid',
                'description' => '秋葉原にある人気のメイドカフェです。明るく楽しい雰囲気で、初心者の方も安心して働けます。',
                'opening_hours' => "平日: 11:00-22:00\n土日祝: 10:00-23:00",
                'status' => 'active',
            ]
        );

        // テスト店舗2: 執事喫茶
        $shop2 = Shop::firstOrCreate(
            ['name' => '執事喫茶 ロイヤル'],
            [
                'prefecture_id' => $tokyo->id,
                'city_id' => null,
                'address' => '銀座2-2-2',
                'phone' => '03-2345-6789',
                'email' => 'shop2@example.com',
                'concept_type' => 'butler',
                'uniform_type' => 'butler',
                'description' => '上品な雰囲気の執事喫茶です。接客スキルを身につけたい方におすすめです。',
                'opening_hours' => "平日: 12:00-21:00\n土日祝: 11:00-22:00",
                'status' => 'active',
            ]
        );

        // テスト店舗3: アイドルカフェ
        $shop3 = Shop::firstOrCreate(
            ['name' => 'アイドルカフェ スター'],
            [
                'prefecture_id' => $osaka->id,
                'city_id' => null,
                'address' => '難波3-3-3',
                'phone' => '06-3456-7890',
                'email' => 'shop3@example.com',
                'concept_type' => 'idol',
                'uniform_type' => 'idol',
                'description' => '大阪の中心地にあるアイドルカフェです。パフォーマンス経験を活かせます。',
                'opening_hours' => "平日: 11:00-23:00\n土日祝: 10:00-24:00",
                'status' => 'active',
            ]
        );

        // テスト求人1: メイドカフェのアルバイト
        Job::firstOrCreate(
            [
                'shop_id' => $shop1->id,
                'title' => 'メイドカフェスタッフ募集（アルバイト）',
            ],
            [
                'job_type' => 'part_time',
                'salary_min' => 1000,
                'salary_max' => 1200,
                'gender_requirement' => 'female',
                'age_min' => 18,
                'age_max' => 30,
                'work_hours' => "シフト制\n平日: 11:00-22:00\n土日祝: 10:00-23:00\n週3日以上勤務可能な方",
                'description' => '明るく元気なメイドカフェスタッフを募集しています。接客が好きな方、人と接するのが好きな方大歓迎です。未経験者も丁寧に指導いたします。',
                'requirements' => "・18歳以上30歳以下の女性\n・明るく元気な方\n・接客が好きな方\n・週3日以上勤務可能な方\n・未経験者歓迎",
                'benefits' => "・交通費全額支給\n・食事補助あり\n・制服貸与\n・研修制度あり\n・昇給あり",
                'application_deadline' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        // テスト求人2: 執事喫茶の正社員
        Job::firstOrCreate(
            [
                'shop_id' => $shop2->id,
                'title' => '執事喫茶スタッフ募集（正社員）',
            ],
            [
                'job_type' => 'full_time',
                'salary_min' => 250000,
                'salary_max' => 300000,
                'gender_requirement' => 'any',
                'age_min' => 20,
                'age_max' => 35,
                'work_hours' => "シフト制\n平日: 12:00-21:00\n土日祝: 11:00-22:00\n週5日勤務",
                'description' => '上品な雰囲気の執事喫茶で、接客スキルを身につけながら働けます。正社員として安定した働き方ができます。',
                'requirements' => "・20歳以上35歳以下\n・接客経験がある方優遇\n・丁寧な接客ができる方\n・週5日勤務可能な方",
                'benefits' => "・社会保険完備\n・交通費全額支給\n・有給休暇あり\n・昇給・賞与あり\n・研修制度充実",
                'application_deadline' => now()->addMonths(2),
                'status' => 'active',
            ]
        );

        // テスト求人3: アイドルカフェのアルバイト
        Job::firstOrCreate(
            [
                'shop_id' => $shop3->id,
                'title' => 'アイドルカフェスタッフ募集（アルバイト）',
            ],
            [
                'job_type' => 'part_time',
                'salary_min' => 1100,
                'salary_max' => 1300,
                'gender_requirement' => 'female',
                'age_min' => 18,
                'age_max' => 28,
                'work_hours' => "シフト制\n平日: 11:00-23:00\n土日祝: 10:00-24:00\n週2日以上勤務可能な方",
                'description' => 'パフォーマンスが好きな方、アイドルに憧れている方大歓迎！楽しく働きながらスキルアップできます。',
                'requirements' => "・18歳以上28歳以下の女性\n・明るく元気な方\n・パフォーマンスが好きな方\n・週2日以上勤務可能な方\n・未経験者歓迎",
                'benefits' => "・交通費全額支給\n・食事補助あり\n・衣装貸与\n・パフォーマンス研修あり\n・昇給あり",
                'application_deadline' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        // テスト求人4: メイドカフェの契約社員
        Job::firstOrCreate(
            [
                'shop_id' => $shop1->id,
                'title' => 'メイドカフェスタッフ募集（契約社員）',
            ],
            [
                'job_type' => 'contract',
                'salary_min' => 200000,
                'salary_max' => 250000,
                'gender_requirement' => 'female',
                'age_min' => 18,
                'age_max' => 30,
                'work_hours' => "シフト制\n平日: 11:00-22:00\n土日祝: 10:00-23:00\n週4日以上勤務",
                'description' => '契約社員として安定した働き方ができます。経験を積んで正社員への道も開けます。',
                'requirements' => "・18歳以上30歳以下の女性\n・接客経験がある方優遇\n・週4日以上勤務可能な方\n・長期的に働ける方",
                'benefits' => "・社会保険完備\n・交通費全額支給\n・有給休暇あり\n・昇給あり\n・正社員登用制度あり",
                'application_deadline' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        // テスト求人5: 執事喫茶のアルバイト
        Job::firstOrCreate(
            [
                'shop_id' => $shop2->id,
                'title' => '執事喫茶スタッフ募集（アルバイト）',
            ],
            [
                'job_type' => 'part_time',
                'salary_min' => 1050,
                'salary_max' => 1250,
                'gender_requirement' => 'any',
                'age_min' => 18,
                'age_max' => 35,
                'work_hours' => "シフト制\n平日: 12:00-21:00\n土日祝: 11:00-22:00\n週2日以上勤務可能な方",
                'description' => '上品な雰囲気の執事喫茶で、接客スキルを身につけながら働けます。学生の方も大歓迎です。',
                'requirements' => "・18歳以上35歳以下\n・丁寧な接客ができる方\n・週2日以上勤務可能な方\n・未経験者歓迎",
                'benefits' => "・交通費全額支給\n・食事補助あり\n・制服貸与\n・研修制度あり\n・昇給あり",
                'application_deadline' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        // テスト店舗管理者アカウント作成
        // 店舗1の管理者
        ShopAdmin::firstOrCreate(
            ['email' => 'shop1@example.com'],
            [
                'shop_id' => $shop1->id,
                'username' => 'shop1_admin',
                'password_hash' => Hash::make('shop123'),
                'status' => 'active',
            ]
        );

        // 店舗2の管理者
        ShopAdmin::firstOrCreate(
            ['email' => 'shop2@example.com'],
            [
                'shop_id' => $shop2->id,
                'username' => 'shop2_admin',
                'password_hash' => Hash::make('shop123'),
                'status' => 'active',
            ]
        );

        // 店舗3の管理者
        ShopAdmin::firstOrCreate(
            ['email' => 'shop3@example.com'],
            [
                'shop_id' => $shop3->id,
                'username' => 'shop3_admin',
                'password_hash' => Hash::make('shop123'),
                'status' => 'active',
            ]
        );

        $this->command->info('テストデータの作成が完了しました！');
        $this->command->info('テストユーザー: test_user / test123');
        $this->command->info('店舗管理者1: shop1_admin / shop123 (メール: shop1@example.com)');
        $this->command->info('店舗管理者2: shop2_admin / shop123 (メール: shop2@example.com)');
        $this->command->info('店舗管理者3: shop3_admin / shop123 (メール: shop3@example.com)');
    }
}

