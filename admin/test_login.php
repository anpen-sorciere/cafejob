<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>システム管理者ログインテスト</h1>";

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

echo "<h2>3. 管理者認証テスト</h2>";
if (function_exists('is_admin')) {
    $is_admin = is_admin();
    echo "is_admin()結果: " . ($is_admin ? 'true' : 'false') . "<br>";
    
    if ($is_admin) {
        echo "✅ 管理者として認証されています<br>";
        echo "管理者ID: " . ($_SESSION['admin_id'] ?? 'NOT SET') . "<br>";
        echo "管理者ユーザー名: " . ($_SESSION['admin_username'] ?? 'NOT SET') . "<br>";
        echo "管理者ロール: " . ($_SESSION['admin_role'] ?? 'NOT SET') . "<br>";
    } else {
        echo "❌ 管理者として認証されていません<br>";
    }
} else {
    echo "❌ is_admin()関数が存在しません<br>";
}

echo "<h2>4. データベース接続テスト</h2>";
try {
    $result = $db->fetch("SELECT COUNT(*) as count FROM admins");
    echo "✅ データベース接続正常: 管理者数 " . $result['count'] . "件<br>";
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>5. 管理者データ確認</h2>";
try {
    $admin = $db->fetch("SELECT * FROM admins WHERE username = 'admin'");
    if ($admin) {
        echo "✅ 管理者データ存在: " . $admin['username'] . "<br>";
        echo "メール: " . $admin['email'] . "<br>";
        echo "ロール: " . $admin['role'] . "<br>";
        echo "ステータス: " . $admin['status'] . "<br>";
    } else {
        echo "❌ 管理者データが存在しません<br>";
    }
} catch (Exception $e) {
    echo "❌ 管理者データ取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>6. テスト完了</h2>";
echo "<p><a href='../?page=admin_login'>システム管理者ログイン</a></p>";
echo "<p><a href='test_basic.php'>基本テスト</a></p>";
?>
