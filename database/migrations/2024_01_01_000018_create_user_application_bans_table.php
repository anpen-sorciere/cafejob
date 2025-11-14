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
        $tableExists = DB::select("SHOW TABLES LIKE 'user_application_bans'");
        
        if (empty($tableExists)) {
            // 外部キー制約なしでテーブルを作成
            DB::statement("
                CREATE TABLE user_application_bans (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NOT NULL,
                    shop_id INT NOT NULL,
                    user_report_id INT NULL,
                    reason VARCHAR(255) NULL,
                    banned_until DATETIME NOT NULL,
                    banned_by INT NOT NULL,
                    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
                    revoked_by INT NULL,
                    revoked_at DATETIME NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_shop_id (shop_id),
                    INDEX idx_user_report_id (user_report_id),
                    INDEX idx_banned_by (banned_by),
                    INDEX idx_banned_until (banned_until),
                    INDEX idx_status (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // 外部キー制約を後から追加
            try {
                DB::statement("ALTER TABLE user_application_bans ADD CONSTRAINT fk_user_application_bans_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_application_bans ADD CONSTRAINT fk_user_application_bans_shop FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_application_bans ADD CONSTRAINT fk_user_application_bans_report FOREIGN KEY (user_report_id) REFERENCES user_reports(id) ON DELETE SET NULL");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_application_bans ADD CONSTRAINT fk_user_application_bans_banned_by FOREIGN KEY (banned_by) REFERENCES shop_admins(id) ON DELETE CASCADE");
            } catch (\Exception $e) {
                // 外部キー制約の追加に失敗しても続行
            }

            try {
                DB::statement("ALTER TABLE user_application_bans ADD CONSTRAINT fk_user_application_bans_revoked_by FOREIGN KEY (revoked_by) REFERENCES admins(id) ON DELETE SET NULL");
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
        Schema::dropIfExists('user_application_bans');
    }
};

