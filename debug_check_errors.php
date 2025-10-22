<?php
// エラーログ確認用ファイル
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>エラーログ確認</h1>";

// PHP設定確認
echo "<h2>PHP設定</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Error Reporting:</strong> " . ini_get('error_reporting') . "</p>";
echo "<p><strong>Display Errors:</strong> " . ini_get('display_errors') . "</p>";
echo "<p><strong>Log Errors:</strong> " . ini_get('log_errors') . "</p>";
echo "<p><strong>Error Log:</strong> " . ini_get('error_log') . "</p>";

// エラーログファイルの確認
$error_log_path = __DIR__ . '/logs/php_errors.log';
echo "<h2>エラーログファイル</h2>";
echo "<p><strong>パス:</strong> " . $error_log_path . "</p>";

if (file_exists($error_log_path)) {
    echo "<p><strong>ファイルサイズ:</strong> " . filesize($error_log_path) . " bytes</p>";
    echo "<p><strong>最終更新:</strong> " . date('Y-m-d H:i:s', filemtime($error_log_path)) . "</p>";
    
    $log_content = file_get_contents($error_log_path);
    if (!empty($log_content)) {
        echo "<h3>最新のエラーログ（最後の50行）</h3>";
        $lines = explode("\n", $log_content);
        $recent_lines = array_slice($lines, -50);
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto; font-size: 12px;'>";
        echo htmlspecialchars(implode("\n", $recent_lines));
        echo "</pre>";
    } else {
        echo "<p>エラーログファイルは空です。</p>";
    }
} else {
    echo "<p>エラーログファイルが存在しません。</p>";
}

// Apacheエラーログの確認
echo "<h2>Apacheエラーログ</h2>";
$apache_log_paths = [
    '/var/log/apache2/error.log',
    '/var/log/httpd/error_log',
    '/usr/local/apache2/logs/error_log',
    '/home/purplelion51/logs/error.log'
];

foreach ($apache_log_paths as $path) {
    if (file_exists($path)) {
        echo "<p><strong>Apacheログ:</strong> " . $path . "</p>";
        $apache_log = file_get_contents($path);
        if (!empty($apache_log)) {
            $lines = explode("\n", $apache_log);
            $recent_lines = array_slice($lines, -20);
            echo "<h3>最新のApacheエラーログ（最後の20行）</h3>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto; font-size: 12px;'>";
            echo htmlspecialchars(implode("\n", $recent_lines));
            echo "</pre>";
        }
        break;
    }
}

// ファイル存在確認
echo "<h2>ファイル存在確認</h2>";
$files_to_check = [
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'shop_admin/dashboard.php',
    'pages/shop_login.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    echo "<p><strong>" . $file . ":</strong> " . (file_exists($full_path) ? "存在" : "不存在") . "</p>";
}

// セッション情報
echo "<h2>セッション情報</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";

// セッションデータ
echo "<h3>セッションデータ</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
print_r($_SESSION);
echo "</pre>";

// サーバー情報
echo "<h2>サーバー情報</h2>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// データベース接続テスト
echo "<h2>データベース接続テスト</h2>";
try {
    require_once 'config/config.php';
    require_once 'config/database.php';
    $db = new Database();
    echo "<p style='color: green;'>データベース接続成功</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>データベース接続エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 関数存在確認
echo "<h2>関数存在確認</h2>";
try {
    require_once 'includes/functions.php';
    echo "<p style='color: green;'>functions.php読み込み成功</p>";
    
    $functions_to_check = ['is_shop_admin', 'sanitize_input', 'verify_password'];
    foreach ($functions_to_check as $func) {
        echo "<p><strong>" . $func . ":</strong> " . (function_exists($func) ? "存在" : "不存在") . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>functions.php読み込みエラー: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 現在のディレクトリ
echo "<h2>現在のディレクトリ</h2>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script Directory:</strong> " . __DIR__ . "</p>";

// 権限確認
echo "<h2>権限確認</h2>";
echo "<p><strong>Logs Directory:</strong> " . (is_writable(__DIR__ . '/logs') ? "書き込み可能" : "書き込み不可") . "</p>";
echo "<p><strong>Current Directory:</strong> " . (is_writable(__DIR__) ? "書き込み可能" : "書き込み不可") . "</p>";

?>
