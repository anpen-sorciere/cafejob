<?php
// 構文チェック用スクリプト
echo "<h1>キャスト管理ページの構文チェック</h1>";

// 1. 基本的なPHP実行テスト
echo "<h2>1. 基本的なPHP実行テスト</h2>";
echo "✅ PHP実行正常";

// 2. セッション開始テスト
echo "<h2>2. セッション開始テスト</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "✅ セッション開始正常";

// 3. ファイル読み込みテスト
echo "<h2>3. ファイル読み込みテスト</h2>";
try {
    require_once '../config/config.php';
    echo "✅ config.php読み込み正常<br>";
} catch (Exception $e) {
    echo "❌ config.php読み込みエラー: " . $e->getMessage() . "<br>";
}

try {
    require_once '../config/database.php';
    echo "✅ database.php読み込み正常<br>";
} catch (Exception $e) {
    echo "❌ database.php読み込みエラー: " . $e->getMessage() . "<br>";
}

try {
    require_once '../includes/functions.php';
    echo "✅ functions.php読み込み正常<br>";
} catch (Exception $e) {
    echo "❌ functions.php読み込みエラー: " . $e->getMessage() . "<br>";
}

// 4. 認証テスト
echo "<h2>4. 認証テスト</h2>";
if (function_exists('is_shop_admin')) {
    $is_admin = is_shop_admin();
    echo "✅ is_shop_admin()実行正常: " . ($is_admin ? '認証済み' : '未認証') . "<br>";
} else {
    echo "❌ is_shop_admin()関数が存在しません<br>";
}

// 5. データベース接続テスト
echo "<h2>5. データベース接続テスト</h2>";
try {
    $shop_id = $_SESSION['shop_id'] ?? null;
    if ($shop_id) {
        $result = $db->fetch("SELECT COUNT(*) as count FROM casts WHERE shop_id = ?", [$shop_id]);
        echo "✅ データベース接続正常: キャスト数 " . $result['count'] . "件<br>";
    } else {
        echo "❌ ショップIDがセッションに設定されていません<br>";
    }
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br>";
}

// 6. 変数設定テスト
echo "<h2>6. 変数設定テスト</h2>";
$page_title = 'キャスト管理';
$shop_id = $_SESSION['shop_id'] ?? null;
$shop_name = $_SESSION['shop_name'] ?? null;
echo "✅ 変数設定正常: page_title='$page_title', shop_id='$shop_id', shop_name='$shop_name'<br>";

// 7. HTML出力テスト
echo "<h2>7. HTML出力テスト</h2>";
try {
    ob_start();
    echo "<!DOCTYPE html><html><head><title>Test</title></head><body><h1>Test</h1></body></html>";
    $html = ob_get_clean();
    echo "✅ HTML出力正常<br>";
} catch (Exception $e) {
    echo "❌ HTML出力エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>構文チェック完了</h2>";
?>
