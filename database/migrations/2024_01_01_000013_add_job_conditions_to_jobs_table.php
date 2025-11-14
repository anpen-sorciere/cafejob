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
        // jobsテーブルに待遇・条件を保存するJSONカラムを追加
        $columns = DB::select("SHOW COLUMNS FROM jobs");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('job_conditions', $columnNames)) {
            DB::statement("ALTER TABLE jobs ADD COLUMN job_conditions JSON NULL AFTER benefits");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM jobs");
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('job_conditions', $columnNames)) {
            DB::statement("ALTER TABLE jobs DROP COLUMN job_conditions");
        }
    }
};

