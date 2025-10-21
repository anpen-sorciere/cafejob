<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Adminディレクトリ基本テスト</h1>";

echo "<h2>1. PHP基本情報</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Script Path: " . __FILE__ . "<br>";

echo "<h2>2. セッション開始</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "✅ セッション開始完了<br>";

echo "<h2>3. ファイル存在確認</h2>";
$files = [
    '../config/config.php',
    '../config/database.php',
    '../includes/functions.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file 存在<br>";
    } else {
        echo "❌ $file 不存在<br>";
    }
}

echo "<h2>4. ファイル読み込みテスト</h2>";
try {
    require_once '../config/config.php';
    echo "✅ config.php読み込み完了<br>";
} catch (Exception $e) {
    echo "❌ config.php読み込みエラー: " . $e->getMessage() . "<br>";
}

try {
    require_once '../config/database.php';
    echo "✅ database.php読み込み完了<br>";
} catch (Exception $e) {
    echo "❌ database.php読み込みエラー: " . $e->getMessage() . "<br>";
}

try {
    require_once '../includes/functions.php';
    echo "✅ functions.php読み込み完了<br>";
} catch (Exception $e) {
    echo "❌ functions.php読み込みエラー: " . $e->getMessage() . "<br>";
}

echo "<h2>5. 管理者認証テスト</h2>";
if (function_exists('is_admin')) {
    $is_admin = is_admin();
    echo "is_admin()結果: " . ($is_admin ? 'true' : 'false') . "<br>";
} else {
    echo "❌ is_admin()関数が存在しません<br>";
}

echo "<h2>6. データベース接続テスト</h2>";
try {
    $result = $db->fetch("SELECT COUNT(*) as count FROM users");
    echo "✅ データベース接続正常: ユーザー数 " . $result['count'] . "件<br>";
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>7. テスト完了</h2>";
echo "<p>このテストが成功すれば、adminディレクトリの問題は特定できます。</p>";
?>
