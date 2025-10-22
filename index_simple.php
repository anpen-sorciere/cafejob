<?php
// 簡易版index.php（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

echo "<h1>カフェJob - 簡易版</h1>";

try {
    // ログアウト処理
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // メインページの表示
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $allowed_pages = ['home', 'search', 'jobs', 'shops', 'cast', 'login', 'register', 'admin_login'];

    if (!in_array($page, $allowed_pages)) {
        $page = 'home';
    }

    echo "<h2>表示するページ: $page</h2>";
    
    // ページファイルの存在確認
    $page_file = "pages/$page.php";
    if (file_exists($page_file)) {
        echo "✅ ページファイル存在: $page_file<br>";
        
        // ページファイルを読み込み
        echo "<h2>ページファイルの読み込み</h2>";
        include $page_file;
        
    } else {
        echo "❌ ページファイル不存在: $page_file<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ エラーが発生しました</h2>";
    echo "<p style='color: red;'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>




