<?php
// エラーログ確認用ファイル
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

echo "<h1>PHP設定確認</h1>";
echo "<h2>エラーログ設定</h2>";
echo "error_reporting: " . error_reporting() . "<br>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "log_errors: " . ini_get('log_errors') . "<br>";
echo "error_log: " . ini_get('error_log') . "<br>";

echo "<h2>PHP情報</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

echo "<h2>セッション情報</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";

echo "<h2>ファイル存在確認</h2>";
$files_to_check = [
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'pages/shop_login.php',
    'shop_admin/dashboard.php'
];

foreach ($files_to_check as $file) {
    echo $file . ": " . (file_exists($file) ? "存在" : "不存在") . "<br>";
}

echo "<h2>データベース接続テスト</h2>";
try {
    require_once 'config/config.php';
    require_once 'config/database.php';
    $db = new Database();
    echo "データベース接続: 成功<br>";
} catch (Exception $e) {
    echo "データベース接続エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>関数テスト</h2>";
try {
    require_once 'includes/functions.php';
    echo "functions.php読み込み: 成功<br>";
    
    if (function_exists('is_shop_admin')) {
        echo "is_shop_admin関数: 存在<br>";
    } else {
        echo "is_shop_admin関数: 不存在<br>";
    }
    
    if (function_exists('sanitize_input')) {
        echo "sanitize_input関数: 存在<br>";
    } else {
        echo "sanitize_input関数: 不存在<br>";
    }
    
    if (function_exists('verify_password')) {
        echo "verify_password関数: 存在<br>";
    } else {
        echo "verify_password関数: 不存在<br>";
    }
} catch (Exception $e) {
    echo "functions.php読み込みエラー: " . $e->getMessage() . "<br>";
}

echo "<h2>セッションデータ</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>POSTデータ</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>GETデータ</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// エラーログファイルの内容を表示
echo "<h2>エラーログファイル内容</h2>";
$error_log_file = __DIR__ . '/logs/php_errors.log';
if (file_exists($error_log_file)) {
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($error_log_file));
    echo "</pre>";
} else {
    echo "エラーログファイルが存在しません: " . $error_log_file . "<br>";
}

// Apacheエラーログも確認
echo "<h2>Apacheエラーログ</h2>";
$apache_error_logs = [
    'C:/xampp/apache/logs/error.log',
    'C:/xampp/logs/error.log',
    '/var/log/apache2/error.log',
    '/var/log/httpd/error_log'
];

foreach ($apache_error_logs as $log_file) {
    if (file_exists($log_file)) {
        echo "Apacheエラーログ: " . $log_file . "<br>";
        $content = file_get_contents($log_file);
        $lines = explode("\n", $content);
        $recent_lines = array_slice($lines, -20); // 最新20行
        echo "<pre>";
        echo htmlspecialchars(implode("\n", $recent_lines));
        echo "</pre>";
        break;
    }
}
?>
