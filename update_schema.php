<?php
// データベーススキーマ更新スクリプト
// 住所確認システム用のカラムを追加

require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>データベーススキーマ更新</h1>";
    
    // shopsテーブルに新しいカラムを追加
    $alter_queries = [
        "ALTER TABLE shops ADD COLUMN verification_code VARCHAR(6) AFTER image_url",
        "ALTER TABLE shops ADD COLUMN verification_sent_at TIMESTAMP NULL AFTER verification_code",
        "ALTER TABLE shops ADD COLUMN verification_verified_at TIMESTAMP NULL AFTER verification_sent_at",
        "ALTER TABLE shops MODIFY COLUMN status ENUM('active', 'inactive', 'pending', 'verification_pending') DEFAULT 'verification_pending'"
    ];
    
    foreach ($alter_queries as $query) {
        try {
            $pdo->exec($query);
            echo "<p style='color: green;'>✅ " . $query . " - 成功</p>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠️ " . $query . " - カラムは既に存在します</p>";
            } else {
                echo "<p style='color: red;'>❌ " . $query . " - エラー: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<h2>更新完了</h2>";
    echo "<p>データベーススキーマの更新が完了しました。</p>";
    echo "<p><a href='?page=shop_register'>店舗登録ページに戻る</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>データベース接続エラー: " . $e->getMessage() . "</p>";
}
?>
