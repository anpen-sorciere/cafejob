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
        // 既存のNULL値を16歳以上の日付に更新（テストデータ用）
        DB::statement("UPDATE users SET birth_date = DATE_SUB(CURDATE(), INTERVAL 20 YEAR) WHERE birth_date IS NULL");
        
        // birth_dateを必須に変更（直接SQLで実行）
        DB::statement("ALTER TABLE users MODIFY COLUMN birth_date DATE NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN birth_date DATE NULL");
    }
};

