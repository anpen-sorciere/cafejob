<?php
// 超基本的なPHPテスト
echo "PHP動作確認: OK<br>";
echo "現在時刻: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

// ファイル存在確認
echo "<br>ファイル確認:<br>";
echo "config.php: " . (file_exists('config/config.php') ? '存在' : '不存在') . "<br>";
echo "database.php: " . (file_exists('config/database.php') ? '存在' : '不存在') . "<br>";
echo "index.php: " . (file_exists('index.php') ? '存在' : '不存在') . "<br>";

// 設定ファイル読み込みテスト
echo "<br>設定読み込みテスト:<br>";
try {
    require_once 'config/config.php';
    echo "config.php読み込み: 成功<br>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '未定義') . "<br>";
} catch (Exception $e) {
    echo "config.php読み込み: エラー - " . $e->getMessage() . "<br>";
}

// データベース接続テスト
echo "<br>データベース接続テスト:<br>";
try {
    $pdo = new PDO(
        "mysql:host=mysql2103.db.sakura.ne.jp;dbname=purplelion51_cafejob;charset=utf8mb4",
        "purplelion51",
        "-6r_am73"
    );
    echo "データベース接続: 成功<br>";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "テーブル数: " . count($tables) . "<br>";
    
} catch (Exception $e) {
    echo "データベース接続: エラー - " . $e->getMessage() . "<br>";
}

echo "<br>テスト完了<br>";
?>
