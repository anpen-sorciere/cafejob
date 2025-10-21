<?php
// 詳細エラー診断ファイル
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

echo "<h1>PHP詳細診断</h1>";

// PHP情報
echo "<h2>PHP情報</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? '不明') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? '不明') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? '不明') . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? '不明') . "<br>";

// ファイル存在確認
echo "<h2>ファイル存在確認</h2>";
$files = [
    'index.php',
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'includes/layout.php',
    '.htaccess'
];

foreach ($files as $file) {
    $exists = file_exists($file);
    $readable = $exists ? is_readable($file) : false;
    echo "$file: " . ($exists ? "✓存在" : "✗不存在") . 
         ($readable ? " ✓読取可能" : " ✗読取不可") . "<br>";
}

// ディレクトリ確認
echo "<h2>ディレクトリ確認</h2>";
$dirs = ['config', 'includes', 'pages', 'admin', 'uploads', 'logs'];
foreach ($dirs as $dir) {
    $exists = is_dir($dir);
    $readable = $exists ? is_readable($dir) : false;
    echo "$dir: " . ($exists ? "✓存在" : "✗不存在") . 
         ($readable ? " ✓読取可能" : " ✗読取不可") . "<br>";
}

// 設定ファイル読み込みテスト
echo "<h2>設定ファイル読み込みテスト</h2>";
try {
    if (file_exists('config/config.php')) {
        require_once 'config/config.php';
        echo "config.php: ✓読み込み成功<br>";
        echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '未定義') . "<br>";
        echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : '未定義') . "<br>";
        echo "DB_USER: " . (defined('DB_USER') ? DB_USER : '未定義') . "<br>";
        echo "DEBUG_MODE: " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'true' : 'false') : '未定義') . "<br>";
    } else {
        echo "config.php: ✗ファイルが存在しません<br>";
    }
} catch (Exception $e) {
    echo "config.php: ✗エラー - " . $e->getMessage() . "<br>";
}

// データベース接続テスト
echo "<h2>データベース接続テスト</h2>";
try {
    if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        echo "データベース接続: ✓成功<br>";
        
        // テーブル確認
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "テーブル数: " . count($tables) . "<br>";
        if (count($tables) > 0) {
            echo "テーブル一覧: " . implode(', ', array_slice($tables, 0, 10)) . 
                 (count($tables) > 10 ? '...' : '') . "<br>";
        }
    } else {
        echo "データベース接続: ✗設定が不完全<br>";
    }
} catch (Exception $e) {
    echo "データベース接続: ✗エラー - " . $e->getMessage() . "<br>";
}

// index.php読み込みテスト
echo "<h2>index.php読み込みテスト</h2>";
try {
    if (file_exists('index.php')) {
        // 出力バッファリングを開始
        ob_start();
        
        // セッションを開始せずにテスト
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['page'] = 'home';
        
        // index.phpの内容を読み込んで実行
        $index_content = file_get_contents('index.php');
        
        // 危険な部分をコメントアウト
        $index_content = preg_replace('/session_start\(\);/', '// session_start();', $index_content);
        
        // 一時ファイルに保存して実行
        $temp_file = 'temp_index_test.php';
        file_put_contents($temp_file, $index_content);
        
        include $temp_file;
        
        $output = ob_get_clean();
        echo "index.php: ✓実行成功<br>";
        echo "出力長: " . strlen($output) . " 文字<br>";
        
        // 一時ファイルを削除
        unlink($temp_file);
        
    } else {
        echo "index.php: ✗ファイルが存在しません<br>";
    }
} catch (Exception $e) {
    echo "index.php: ✗エラー - " . $e->getMessage() . "<br>";
    if (ob_get_level()) {
        ob_end_clean();
    }
}

// エラーログ確認
echo "<h2>エラーログ確認</h2>";
if (file_exists('php_errors.log')) {
    $log_content = file_get_contents('php_errors.log');
    echo "エラーログサイズ: " . strlen($log_content) . " 文字<br>";
    if (strlen($log_content) > 0) {
        echo "最新のエラー:<br>";
        echo "<pre>" . htmlspecialchars(substr($log_content, -1000)) . "</pre>";
    }
} else {
    echo "エラーログファイルが存在しません<br>";
}

echo "<h2>診断完了</h2>";
echo "このファイルが表示されていれば、基本的なPHPは動作しています。<br>";
?>
