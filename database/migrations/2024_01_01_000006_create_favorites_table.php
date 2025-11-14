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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // shop_id と job_id のどちらか一方のみが設定されることを保証（MySQLのCHECK制約）
        DB::statement('ALTER TABLE favorites ADD CONSTRAINT check_favorite_type CHECK ((shop_id IS NOT NULL AND job_id IS NULL) OR (shop_id IS NULL AND job_id IS NOT NULL))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};

