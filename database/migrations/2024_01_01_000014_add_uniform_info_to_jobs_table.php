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
        // jobsテーブルに制服・コスプレ情報を保存するカラムを追加
        $columns = DB::select("SHOW COLUMNS FROM jobs");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('uniform_description', $columnNames)) {
            DB::statement("ALTER TABLE jobs ADD COLUMN uniform_description TEXT NULL AFTER job_conditions");
        }
        
        if (!in_array('uniform_images', $columnNames)) {
            DB::statement("ALTER TABLE jobs ADD COLUMN uniform_images JSON NULL AFTER uniform_description");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM jobs");
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('uniform_images', $columnNames)) {
            DB::statement("ALTER TABLE jobs DROP COLUMN uniform_images");
        }
        
        if (in_array('uniform_description', $columnNames)) {
            DB::statement("ALTER TABLE jobs DROP COLUMN uniform_description");
        }
    }
};

