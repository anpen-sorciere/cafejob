<?php
// 詳細ログ出力ファイル
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// ログファイルのパス
$log_file = __DIR__ . '/logs/detailed_debug.log';

// ログ出力関数
function write_log($message, $data = null) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";
    if ($data !== null) {
        $log_message .= " | Data: " . print_r($data, true);
    }
    $log_message .= "\n";
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

// ログファイルをクリア
file_put_contents($log_file, '');

write_log("=== 詳細デバッグ開始 ===");

try {
    write_log("1. セッション処理開始");
    
    // セッション名を指定して開始
    if (session_status() === PHP_SESSION_NONE) {
        session_name('cafejob_session');
        session_start();
        write_log("セッション開始成功", ['session_name' => session_name(), 'session_id' => session_id()]);
    } else {
        write_log("セッション既に開始済み", ['session_name' => session_name(), 'session_id' => session_id()]);
    }
    
    write_log("2. セッションデータ確認");
    write_log("セッションデータ", $_SESSION);
    
    write_log("3. ファイル読み込み開始");
    
    // config.php読み込み
    write_log("config.php読み込み試行");
    if (file_exists('config/config.php')) {
        require_once 'config/config.php';
        write_log("config.php読み込み成功");
    } else {
        write_log("config.phpファイル不存在");
        throw new Exception("config.phpファイルが見つかりません");
    }
    
    // database.php読み込み
    write_log("database.php読み込み試行");
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        write_log("database.php読み込み成功");
    } else {
        write_log("database.phpファイル不存在");
        throw new Exception("database.phpファイルが見つかりません");
    }
    
    // functions.php読み込み
    write_log("functions.php読み込み試行");
    if (file_exists('includes/functions.php')) {
        require_once 'includes/functions.php';
        write_log("functions.php読み込み成功");
    } else {
        write_log("functions.phpファイル不存在");
        throw new Exception("functions.phpファイルが見つかりません");
    }
    
    write_log("4. データベース接続試行");
    $db = new Database();
    write_log("データベース接続成功");
    
    // データベース情報確認
    $db_info = $db->getConnection()->query("SELECT DATABASE() as db_name")->fetch();
    write_log("接続データベース", $db_info);
    
    write_log("5. 店舗管理者認証チェック");
    if (function_exists('is_shop_admin')) {
        $is_admin = is_shop_admin();
        write_log("is_shop_admin関数実行結果", $is_admin);
        
        if ($is_admin) {
            write_log("店舗管理者認証成功");
            write_log("店舗管理者セッションデータ", [
                'shop_admin_id' => $_SESSION['shop_admin_id'] ?? 'not_set',
                'shop_id' => $_SESSION['shop_id'] ?? 'not_set',
                'shop_name' => $_SESSION['shop_name'] ?? 'not_set',
                'shop_status' => $_SESSION['shop_status'] ?? 'not_set'
            ]);
        } else {
            write_log("店舗管理者認証失敗");
        }
    } else {
        write_log("is_shop_admin関数が存在しません");
    }
    
    write_log("6. テーブル存在確認");
    $tables = $db->fetchAll("SHOW TABLES");
    $table_names = array_map(function($table) { return array_values($table)[0]; }, $tables);
    write_log("存在するテーブル", $table_names);
    
    // 重要なテーブルの存在確認
    $important_tables = ['shop_admins', 'shops', 'jobs', 'applications', 'users'];
    foreach ($important_tables as $table) {
        if (in_array($table, $table_names)) {
            write_log("テーブル $table 存在");
            
            // テーブルのレコード数を確認
            try {
                $count = $db->fetch("SELECT COUNT(*) as count FROM $table")['count'];
                write_log("テーブル $table のレコード数", $count);
            } catch (Exception $e) {
                write_log("テーブル $table のレコード数取得エラー", $e->getMessage());
            }
        } else {
            write_log("テーブル $table 不存在");
        }
    }
    
    write_log("7. 店舗管理者データ確認");
    if (in_array('shop_admins', $table_names)) {
        try {
            $shop_admins = $db->fetchAll("SELECT id, email, username, status FROM shop_admins LIMIT 3");
            write_log("店舗管理者データ（最初の3件）", $shop_admins);
        } catch (Exception $e) {
            write_log("店舗管理者データ取得エラー", $e->getMessage());
        }
    }
    
    write_log("8. 店舗データ確認");
    if (in_array('shops', $table_names)) {
        try {
            $shops = $db->fetchAll("SELECT id, name, status FROM shops LIMIT 3");
            write_log("店舗データ（最初の3件）", $shops);
        } catch (Exception $e) {
            write_log("店舗データ取得エラー", $e->getMessage());
        }
    }
    
    write_log("9. POST/GETデータ確認");
    write_log("POSTデータ", $_POST);
    write_log("GETデータ", $_GET);
    
    write_log("10. サーバー情報");
    write_log("サーバー情報", [
        'PHP_VERSION' => PHP_VERSION,
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'unknown',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'unknown',
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'unknown'
    ]);
    
    write_log("=== 詳細デバッグ完了 ===");
    
    echo "<h1>詳細ログ出力完了</h1>";
    echo "<p>ログファイル: <code>$log_file</code></p>";
    echo "<p>ログの内容を確認してください。</p>";
    
    // ログファイルの内容を表示
    if (file_exists($log_file)) {
        echo "<h2>ログ内容</h2>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 500px; overflow-y: auto;'>";
        echo htmlspecialchars(file_get_contents($log_file));
        echo "</pre>";
    }
    
} catch (Exception $e) {
    write_log("=== エラー発生 ===");
    write_log("エラーメッセージ", $e->getMessage());
    write_log("エラーファイル", $e->getFile());
    write_log("エラー行", $e->getLine());
    write_log("スタックトレース", $e->getTraceAsString());
    
    echo "<h1>エラー発生</h1>";
    echo "<p>エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>エラーファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>エラー行: " . $e->getLine() . "</p>";
    
    // ログファイルの内容を表示
    if (file_exists($log_file)) {
        echo "<h2>ログ内容</h2>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 500px; overflow-y: auto;'>";
        echo htmlspecialchars(file_get_contents($log_file));
        echo "</pre>";
    }
}
?>
