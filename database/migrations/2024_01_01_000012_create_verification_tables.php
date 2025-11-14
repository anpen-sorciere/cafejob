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
        // verification_attemptsテーブル
        if (!Schema::hasTable('verification_attempts')) {
            Schema::create('verification_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained()->onDelete('cascade');
                $table->enum('attempt_type', ['initial_registration', 'address_change'])->default('initial_registration');
                $table->string('verification_code', 6)->nullable();
                $table->string('input_code', 6)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->boolean('is_successful')->default(false);
                $table->timestamp('attempt_time')->useCurrent();
                
                $table->index('shop_id');
                $table->index('attempt_time');
            });
        }

        // shop_address_changesテーブル（存在しない場合のみ作成）
        if (!Schema::hasTable('shop_address_changes')) {
            Schema::create('shop_address_changes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained()->onDelete('cascade');
                $table->string('old_postal_code', 7)->nullable();
                $table->foreignId('old_prefecture_id')->nullable()->constrained('prefectures')->nullOnDelete();
                $table->foreignId('old_city_id')->nullable()->constrained('cities')->nullOnDelete();
                $table->string('old_address', 200)->nullable();
                $table->string('new_postal_code', 7);
                $table->foreignId('new_prefecture_id')->constrained('prefectures');
                $table->foreignId('new_city_id')->constrained('cities');
                $table->string('new_address', 200);
                $table->string('verification_code', 6);
                $table->integer('failed_attempts')->default(0);
                $table->boolean('is_locked')->default(false);
                $table->timestamp('locked_at')->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                
                $table->index('shop_id');
                $table->index('status');
                $table->index('verification_code');
            });
        } else {
            // 既存テーブルにカラムを追加（存在しない場合のみ）
            $columns = DB::select("SHOW COLUMNS FROM shop_address_changes");
            $columnNames = array_column($columns, 'Field');
            
            if (!in_array('failed_attempts', $columnNames)) {
                DB::statement("ALTER TABLE shop_address_changes ADD COLUMN failed_attempts INT DEFAULT 0 AFTER verification_code");
            }
            if (!in_array('is_locked', $columnNames)) {
                DB::statement("ALTER TABLE shop_address_changes ADD COLUMN is_locked BOOLEAN DEFAULT FALSE AFTER failed_attempts");
            }
            if (!in_array('locked_at', $columnNames)) {
                DB::statement("ALTER TABLE shop_address_changes ADD COLUMN locked_at TIMESTAMP NULL AFTER is_locked");
            }
        }

        // shopsテーブルに住所確認状態カラムを追加（存在しない場合のみ）
        $shopColumns = DB::select("SHOW COLUMNS FROM shops");
        $shopColumnNames = array_column($shopColumns, 'Field');
        
        if (!in_array('address_verification_status', $shopColumnNames)) {
            DB::statement("ALTER TABLE shops ADD COLUMN address_verification_status ENUM('verified', 'pending', 'locked') DEFAULT 'verified' AFTER status");
        }
        if (!in_array('address_verification_locked_at', $shopColumnNames)) {
            DB::statement("ALTER TABLE shops ADD COLUMN address_verification_locked_at TIMESTAMP NULL AFTER address_verification_status");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_attempts');
        Schema::dropIfExists('shop_address_changes');
    }
};

