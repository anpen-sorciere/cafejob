<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>キャスト管理認証デバッグ</h1>";

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>1. セッション情報</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>2. ファイル読み込みテスト</h2>";
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

echo "<h2>3. 認証チェック</h2>";
if (function_exists('is_shop_admin')) {
    $is_admin = is_shop_admin();
    echo "is_shop_admin()結果: " . ($is_admin ? 'true' : 'false') . "<br>";
    
    if ($is_admin) {
        echo "✅ 店舗管理者として認証されています<br>";
    } else {
        echo "❌ 店舗管理者として認証されていません<br>";
        
        // 詳細な認証チェック
        echo "<h3>詳細な認証チェック</h3>";
        echo "shop_admin_id: " . ($_SESSION['shop_admin_id'] ?? 'NOT SET') . "<br>";
        echo "shop_id: " . ($_SESSION['shop_id'] ?? 'NOT SET') . "<br>";
        echo "shop_name: " . ($_SESSION['shop_name'] ?? 'NOT SET') . "<br>";
        echo "shop_status: " . ($_SESSION['shop_status'] ?? 'NOT SET') . "<br>";
    }
} else {
    echo "❌ is_shop_admin()関数が存在しません<br>";
}

echo "<h2>4. リダイレクト先</h2>";
echo "認証失敗時のリダイレクト先: ../?page=shop_admin_login<br>";
?>
