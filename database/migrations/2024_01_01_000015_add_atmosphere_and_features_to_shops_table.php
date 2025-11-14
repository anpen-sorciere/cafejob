<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // shopsテーブルにお店の雰囲気画像とアルバイトの特徴を保存するカラムを追加
        $columns = DB::select("SHOW COLUMNS FROM shops");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('atmosphere_images', $columnNames)) {
            DB::statement("ALTER TABLE shops ADD COLUMN atmosphere_images JSON NULL AFTER image_url");
        }
        
        if (!in_array('job_features', $columnNames)) {
            DB::statement("ALTER TABLE shops ADD COLUMN job_features TEXT NULL AFTER atmosphere_images");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM shops");
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('job_features', $columnNames)) {
            DB::statement("ALTER TABLE shops DROP COLUMN job_features");
        }
        
        if (in_array('atmosphere_images', $columnNames)) {
            DB::statement("ALTER TABLE shops DROP COLUMN atmosphere_images");
        }
    }
};

