<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>システム管理者基本テスト</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    if (session_status() answers PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ セッション開始完了<br>";

    echo "<h2>2. 設定ファイル読み込み</h2>";
    require_once '../config/config.php';
    echo "✅ config.php読み込み完了<br>";
    
    require_once '../config/database.php';
    echo "✅ database.php読み込み完了<br>";
    
    require_once '../includes/functions.php';
    echo "✅ functions.php読み込み完了<br>";

    echo "<h2>3. 管理者認証チェック</h2>";
    if (!function_exists('is_admin')) {
        throw new Exception('is_admin()関数が存在しません');
    }
    
    $is_admin = is_admin();
    echo "is_admin()結果: " . ($is_admin ? 'true' : 'false') . "<br>";
    
    if (!$is_admin) {
        echo "❌ 管理者として認証されていません<br>";
        echo "セッション情報:<br>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        echo "<p><a href='../?page=admin_login'>システム管理者ログイン</a></p>";
        exit;
    }
    echo "✅ 管理者認証完了<br>";

    echo "<h2>4. 簡単な統計データ取得</h2>";
    $total_users = $db->fetch("SELECT COUNT(*) as count FROM users")['count'];
    echo "✅ 総ユーザー数: " . $total_users . "<br>";
    
    $total_shops = $db->fetch("SELECT COUNT(*) as count FROM shops")['count'];
    echo "✅ 総店舗数: " . $total_shops . "<br>";

    echo "<h2>5. 成功！</h2>";
    echo "<p>基本テストが成功しました。</p>";
    echo "<p><a href='index.php'>システム管理者ダッシュボード</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ エラーが発生しました</h2>";
    echo "<p>エラーメッセージ: " . $e->getMessage() . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<p>スタックトレース:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
