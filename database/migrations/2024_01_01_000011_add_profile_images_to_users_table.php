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
        // 既存のprofile_imageカラムがあるかチェック
        $hasProfileImage = DB::select("SHOW COLUMNS FROM users LIKE 'profile_image'");
        
        if (!empty($hasProfileImage)) {
            // 既存のprofile_imageをprofile_image_1にリネーム
            DB::statement("ALTER TABLE users CHANGE COLUMN profile_image profile_image_1 VARCHAR(200) NULL");
        } else {
            // profile_image_1を追加
            DB::statement("ALTER TABLE users ADD COLUMN profile_image_1 VARCHAR(200) NULL AFTER gender");
        }
        
        // profile_image_2を追加（既に存在する場合はスキップ）
        $hasProfileImage2 = DB::select("SHOW COLUMNS FROM users LIKE 'profile_image_2'");
        if (empty($hasProfileImage2)) {
            DB::statement("ALTER TABLE users ADD COLUMN profile_image_2 VARCHAR(200) NULL AFTER profile_image_1");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_image_1')) {
                DB::statement("ALTER TABLE users CHANGE COLUMN profile_image_1 profile_image VARCHAR(200) NULL");
            }
            $table->dropColumn('profile_image_2');
        });
    }
};

