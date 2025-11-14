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
        // テーブルが存在するかチェック
        $tableExists = DB::select("SHOW TABLES LIKE 'user_reports'");
        
        if (empty($tableExists)) {
            // 外部キー制約なしでテーブルを作成
            DB::statement("
                CREATE TABLE user_reports (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    application_id INT NOT NULL,
                    shop_admin_id INT NOT NULL,
                    user_id INT NOT NULL,
                    report_type ENUM('no_show', 'inappropriate_behavior', 'false_information', 'other') DEFAULT 'other',
                    message VARCHAR(30) NOT NULL,
                    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
                    admin_notes TEXT NULL,
                    reviewed_by INT NULL,
                    reviewed_at TIMESTAMP NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_application_id (application_id),
                    INDEX idx_shop_admin_id (shop_admin_id),
                    INDEX idx_user_id (user_id),
                    INDEX idx_reviewed_by (reviewed_by)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // 外部キー制約を後から追加（テーブルが存在する場合のみ）
            try {
                DB::statement("ALTER TABLE user_reports ADD CONSTRAINT fk_user_reports_application FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_reports ADD CONSTRAINT fk_user_reports_shop_admin FOREIGN KEY (shop_admin_id) REFERENCES shop_admins(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_reports ADD CONSTRAINT fk_user_reports_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_reports ADD CONSTRAINT fk_user_reports_reviewed_by FOREIGN KEY (reviewed_by) REFERENCES admins(id) ON DELETE SET NULL");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};

