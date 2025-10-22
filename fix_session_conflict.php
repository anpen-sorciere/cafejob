<?php
// セッション混在問題の解決
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

echo "<h1>セッション混在問題の解決</h1>";

// 現在のセッションを破棄
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
    echo "<h2>現在のセッションを破棄しました</h2>";
}

// cafejob専用のセッションを開始
session_name('cafejob_session');
session_start();

echo "<h2>cafejob専用セッションを開始しました</h2>";
echo "セッション名: " . session_name() . "<br>";
echo "セッションID: " . session_id() . "<br>";

echo "<h2>現在のセッションデータ（クリア後）</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>ファイル読み込みテスト</h2>";
try {
    require_once 'config/config.php';
    echo "config.php: OK<br>";
    
    require_once 'config/database.php';
    echo "database.php: OK<br>";
    
    require_once 'includes/functions.php';
    echo "functions.php: OK<br>";
    
    $db = new Database();
    echo "データベース接続: OK<br>";
    
    // cafejobのデータベース名を確認
    echo "<h2>データベース情報</h2>";
    $db_info = $db->getConnection()->query("SELECT DATABASE() as db_name")->fetch();
    echo "接続中のデータベース: " . $db_info['db_name'] . "<br>";
    
    // cafejobのテーブル存在確認
    echo "<h2>cafejobテーブル存在確認</h2>";
    $tables = $db->fetchAll("SHOW TABLES");
    $cafejob_tables = [];
    foreach ($tables as $table) {
        $table_name = array_values($table)[0];
        $cafejob_tables[] = $table_name;
    }
    
    echo "存在するテーブル:<br>";
    foreach ($cafejob_tables as $table) {
        echo "- " . $table . "<br>";
    }
    
    // 店舗管理者テーブルの確認
    echo "<h2>店舗管理者テーブル確認</h2>";
    if (in_array('shop_admins', $cafejob_tables)) {
        echo "shop_adminsテーブル: 存在<br>";
        
        // 店舗管理者データの確認
        $shop_admins = $db->fetchAll("SELECT id, email, username, status FROM shop_admins LIMIT 5");
        echo "店舗管理者データ（最初の5件）:<br>";
        echo "<pre>";
        print_r($shop_admins);
        echo "</pre>";
    } else {
        echo "shop_adminsテーブル: 不存在<br>";
    }
    
    // 店舗テーブルの確認
    if (in_array('shops', $cafejob_tables)) {
        echo "shopsテーブル: 存在<br>";
        
        $shops = $db->fetchAll("SELECT id, name, status FROM shops LIMIT 5");
        echo "店舗データ（最初の5件）:<br>";
        echo "<pre>";
        print_r($shops);
        echo "</pre>";
    } else {
        echo "shopsテーブル: 不存在<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>エラー発生</h2>";
    echo "エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "エラーファイル: " . $e->getFile() . "<br>";
    echo "エラー行: " . $e->getLine() . "<br>";
}

echo "<h2>推奨アクション</h2>";
echo '<a href="?page=shop_login" class="btn btn-primary">店舗ログイン（セッションクリア後）</a><br>';
echo '<a href="debug_error_logs.php" class="btn btn-secondary">エラーログ確認</a><br>';
?>
