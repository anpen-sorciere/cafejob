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
        // jobsテーブルに体験入店の有無を保存するカラムを追加
        $columns = DB::select("SHOW COLUMNS FROM jobs");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('trial_visit_available', $columnNames)) {
            DB::statement("ALTER TABLE jobs ADD COLUMN trial_visit_available BOOLEAN DEFAULT FALSE AFTER uniform_images");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM jobs");
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('trial_visit_available', $columnNames)) {
            DB::statement("ALTER TABLE jobs DROP COLUMN trial_visit_available");
        }
    }
};

