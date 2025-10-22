<?php
// エラーログクリアと現在の状況確認
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

echo "<h1>エラーログクリアと現在の状況確認</h1>";

// 古いエラーログをクリア
$error_log_file = __DIR__ . '/logs/php_errors.log';
if (file_exists($error_log_file)) {
    file_put_contents($error_log_file, '');
    echo "<h2>エラーログをクリアしました</h2>";
} else {
    echo "<h2>エラーログファイルが存在しません</h2>";
}

echo "<h2>現在の状況確認</h2>";

try {
    // セッション開始
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    echo "<h3>セッションデータ</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    // ファイル読み込みテスト
    echo "<h3>ファイル読み込みテスト</h3>";
    require_once 'config/config.php';
    echo "config.php: OK<br>";
    
    require_once 'config/database.php';
    echo "database.php: OK<br>";
    
    require_once 'includes/functions.php';
    echo "functions.php: OK<br>";
    
    // データベース接続テスト
    echo "<h3>データベース接続テスト</h3>";
    $db = new Database();
    echo "データベース接続: OK<br>";
    
    // 店舗管理者認証テスト
    echo "<h3>店舗管理者認証テスト</h3>";
    if (is_shop_admin()) {
        echo "店舗管理者認証: OK<br>";
        
        $shop_id = $_SESSION['shop_id'];
        $shop_status = $_SESSION['shop_status'];
        $address_verification_pending = isset($_SESSION['address_verification_pending']);
        
        echo "店舗ID: " . $shop_id . "<br>";
        echo "店舗ステータス: " . $shop_status . "<br>";
        echo "住所確認待ち: " . ($address_verification_pending ? "はい" : "いいえ") . "<br>";
        
        // 次のアクションを提案
        echo "<h3>推奨アクション</h3>";
        if ($address_verification_pending || $shop_status === 'verification_pending') {
            echo '<a href="shop_admin/debug_verify_address.php" class="btn btn-primary">住所確認ページ（デバッグ版）</a><br>';
        } else {
            echo '<a href="shop_admin/dashboard.php" class="btn btn-success">ダッシュボード</a><br>';
        }
        
    } else {
        echo "店舗管理者認証: NG<br>";
        echo '<a href="?page=shop_login" class="btn btn-warning">店舗ログイン</a><br>';
    }
    
} catch (Exception $e) {
    echo "<h3>エラー発生</h3>";
    echo "エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "エラーファイル: " . $e->getFile() . "<br>";
    echo "エラー行: " . $e->getLine() . "<br>";
}

echo "<h2>現在のエラーログ</h2>";
if (file_exists($error_log_file)) {
    $content = file_get_contents($error_log_file);
    if (empty($content)) {
        echo "エラーログは空です（正常）<br>";
    } else {
        echo "<pre>";
        echo htmlspecialchars($content);
        echo "</pre>";
    }
} else {
    echo "エラーログファイルが存在しません<br>";
}
?>
