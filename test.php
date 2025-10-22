<?php
// 基本的な動作確認テストファイル
echo "PHP is working!<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

// データベース接続テスト
try {
    $host = 'mysql2103.db.sakura.ne.jp';
    $dbname = 'purplelion51_cafejob';
    $user = 'purplelion51';
    $pass = '-6r_am73';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection: SUCCESS<br>";
    
    // テーブルの存在確認
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "<br>";
    
} catch (PDOException $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "<br>";
}

// ファイル権限確認
echo "File permissions:<br>";
echo "config/config.php: " . (file_exists('config/config.php') ? 'EXISTS' : 'NOT FOUND') . "<br>";
echo "includes/functions.php: " . (file_exists('includes/functions.php') ? 'EXISTS' : 'NOT FOUND') . "<br>";
echo "uploads directory: " . (is_dir('uploads') ? 'EXISTS' : 'NOT FOUND') . "<br>";
echo "logs directory: " . (is_dir('logs') ? 'EXISTS' : 'NOT FOUND') . "<br>";

// セッション確認
session_start();
echo "Session: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . "<br>";
?>




