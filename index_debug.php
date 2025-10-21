<?php
// 簡易版index.php（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>カフェJob - デバッグモード</h1>";

try {
    // 設定ファイルの読み込み
    echo "<h2>1. 設定ファイルの読み込み</h2>";
    require_once 'config/config_simple.php';
    echo "✅ 設定ファイル読み込み成功<br>";
    
    // データベース接続のテスト
    echo "<h2>2. データベース接続テスト</h2>";
    require_once 'config/database.php';
    echo "✅ データベース接続成功<br>";
    
    // 関数ファイルの読み込み
    echo "<h2>3. 関数ファイルの読み込み</h2>";
    require_once 'includes/functions.php';
    echo "✅ 関数ファイル読み込み成功<br>";
    
    // セッション開始
    echo "<h2>4. セッション開始</h2>";
    session_start();
    echo "✅ セッション開始成功<br>";
    
    // 基本的なデータベースクエリテスト
    echo "<h2>5. データベースクエリテスト</h2>";
    $users_count = $db->fetch("SELECT COUNT(*) as count FROM users")['count'];
    echo "✅ ユーザー数: " . $users_count . "<br>";
    
    $shops_count = $db->fetch("SELECT COUNT(*) as count FROM shops")['count'];
    echo "✅ 店舗数: " . $shops_count . "<br>";
    
    $jobs_count = $db->fetch("SELECT COUNT(*) as count FROM jobs")['count'];
    echo "✅ 求人数: " . $jobs_count . "<br>";
    
    echo "<h2>6. ファイル存在確認</h2>";
    $files_to_check = [
        'pages/home.php',
        'pages/jobs.php',
        'pages/shops.php',
        'pages/login.php',
        'pages/register.php',
        'includes/layout.php',
        'assets/css/style.css',
        'assets/js/main.js'
    ];
    
    foreach ($files_to_check as $file) {
        if (file_exists($file)) {
            echo "✅ $file: 存在<br>";
        } else {
            echo "❌ $file: 存在しない<br>";
        }
    }
    
    echo "<h2>7. ディレクトリ権限確認</h2>";
    $dirs_to_check = ['uploads', 'logs'];
    foreach ($dirs_to_check as $dir) {
        if (is_dir($dir)) {
            $perms = substr(sprintf('%o', fileperms($dir)), -4);
            echo "✅ $dir: 存在 (権限: $perms)<br>";
        } else {
            echo "❌ $dir: 存在しない<br>";
        }
    }
    
    echo "<h2>8. 環境情報</h2>";
    echo "PHP Version: " . phpversion() . "<br>";
    echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
    echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
    echo "Script Path: " . __FILE__ . "<br>";
    
    echo "<h2>✅ すべてのテストが完了しました！</h2>";
    echo "<p><a href='index_original.php'>元のindex.phpを試す</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ エラーが発生しました</h2>";
    echo "<p style='color: red;'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>



