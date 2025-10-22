<?php
// デバッグ用index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>カフェJob - デバッグ版</h1>";

try {
    // セッション開始
    echo "<h2>1. セッション開始</h2>";
    session_start();
    echo "✅ セッション開始成功<br>";
    
    // 設定ファイルの読み込み
    echo "<h2>2. 設定ファイルの読み込み</h2>";
    require_once 'config/config_simple.php';
    echo "✅ 設定ファイル読み込み成功<br>";
    
    // データベース接続
    echo "<h2>3. データベース接続</h2>";
    require_once 'config/database.php';
    echo "✅ データベース接続成功<br>";
    
    // 関数ファイルの読み込み
    echo "<h2>4. 関数ファイルの読み込み</h2>";
    require_once 'includes/functions_minimal.php';
    echo "✅ 関数ファイル読み込み成功<br>";
    
    // ログイン状態の確認
    echo "<h2>5. ログイン状態の確認</h2>";
    if (is_logged_in()) {
        echo "✅ ログイン済み<br>";
        echo "User ID: " . $_SESSION['user_id'] . "<br>";
        echo "Username: " . $_SESSION['username'] . "<br>";
    } else {
        echo "❌ ログインしていません<br>";
    }
    
    // ページの決定
    echo "<h2>6. ページの決定</h2>";
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $allowed_pages = ['home', 'search', 'jobs', 'shops', 'cast', 'login', 'register', 'admin_login'];
    
    if (!in_array($page, $allowed_pages)) {
        $page = 'home';
    }
    
    echo "表示するページ: $page<br>";
    
    // ページファイルの存在確認
    echo "<h2>7. ページファイルの存在確認</h2>";
    $page_file = "pages/$page.php";
    if (file_exists($page_file)) {
        echo "✅ ページファイル存在: $page_file<br>";
    } else {
        echo "❌ ページファイル不存在: $page_file<br>";
    }
    
    // レイアウトファイルの存在確認
    echo "<h2>8. レイアウトファイルの存在確認</h2>";
    if (file_exists('includes/layout.php')) {
        echo "✅ レイアウトファイル存在<br>";
    } else {
        echo "❌ レイアウトファイル不存在<br>";
    }
    
    echo "<h2>✅ すべてのチェックが完了しました！</h2>";
    echo "<p><a href='index_original.php'>元のindex.phpを試す</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ エラーが発生しました</h2>";
    echo "<p style='color: red;'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>




