<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // gender_mstテーブルが存在することを確認
        if (!Schema::hasTable('gender_mst')) {
            throw new \Exception('gender_mstテーブルが存在しません。先にgender_mstテーブルを作成してください。');
        }

        // 既存のgenderカラムがenum型の場合、まず削除してから再作成する必要がある
        // 既存のデータを一時的に保存
        if (Schema::hasColumn('users', 'gender')) {
            DB::statement('ALTER TABLE users ADD COLUMN gender_temp VARCHAR(20) NULL');
            
            // 既存のgender値を一時カラムにコピー（enum値を文字列に変換）
            DB::statement("UPDATE users SET gender_temp = CASE 
                WHEN gender = 'female' THEN '女性'
                WHEN gender = 'male' THEN '男性'
                WHEN gender = 'other' THEN 'その他'
                ELSE NULL
            END");
            
            // 古いgenderカラムを削除
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
        
        // 新しいgender_idカラムを外部キーとして追加（既に存在する場合はスキップ）
        if (!Schema::hasColumn('users', 'gender_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('gender_id')->nullable()->after('birth_date')->constrained('gender_mst')->onDelete('set null');
            });
        }
        
        // 既存データがある場合のみ移行処理を実行
        if (Schema::hasColumn('users', 'gender_temp')) {
            // gender_mstテーブルのIDを取得してマッピング
            $genderMap = [
                '女性' => 1,
                '男性' => 2,
                'その他' => 3,
            ];
            
            foreach ($genderMap as $genderName => $genderId) {
                DB::statement("UPDATE users SET gender_id = ? WHERE gender_temp = ?", [$genderId, $genderName]);
            }
            
            // 一時カラムを削除
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender_temp');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 既存のgender_idカラムのデータを一時的に保存
        DB::statement('ALTER TABLE users ADD COLUMN gender_temp VARCHAR(20) NULL');
        
        // gender_idからgender名を取得して一時カラムに保存
        DB::statement("UPDATE users u 
            INNER JOIN gender_mst g ON u.gender_id = g.id 
            SET u.gender_temp = g.gender");
        
        // gender_idカラムを削除
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropColumn('gender_id');
        });
        
        // 古いenum型のgenderカラムを復元
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
        });
        
        // 一時カラムの値をenumに変換して復元
        DB::statement("UPDATE users SET gender = CASE 
            WHEN gender_temp = '女性' THEN 'female'
            WHEN gender_temp = '男性' THEN 'male'
            WHEN gender_temp = 'その他' THEN 'other'
            ELSE NULL
        END");
        
        // 一時カラムを削除
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender_temp');
        });
    }
};
