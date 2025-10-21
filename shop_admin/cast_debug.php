<?php
session_start();

// デバッグモードを有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ログファイルに詳細な情報を記録
$log_file = '../logs/cast_debug.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}

function debug_log($message, $data = null) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    if ($data !== null) {
        $log_entry .= "Data: " . print_r($data, true) . "\n";
    }
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

debug_log('=== キャスト管理デバッグ開始 ===');
debug_log('PHP Version: ' . phpversion());
debug_log('Current Directory: ' . getcwd());
debug_log('Script Path: ' . __FILE__);

try {
    debug_log('1. セッション開始完了');
    
    // 設定ファイル読み込み
    debug_log('2. includes.php読み込み開始');
    require_once 'includes.php';
    debug_log('2. includes.php読み込み完了');
    
    // 認証チェック
    debug_log('3. 認証チェック開始');
    debug_log('Session data', $_SESSION);
    
    if (!function_exists('is_shop_admin')) {
        debug_log('エラー: is_shop_admin関数が存在しません');
        throw new Exception('is_shop_admin関数が存在しません');
    }
    
    $is_admin = is_shop_admin();
    debug_log('is_shop_admin()結果', $is_admin);
    
    if (!$is_admin) {
        debug_log('エラー: 店舗管理者として認証されていません');
        throw new Exception('店舗管理者として認証されていません');
    }
    debug_log('3. 認証チェック完了');
    
    // データベース接続テスト
    debug_log('4. データベース接続テスト開始');
    $shop_id = $_SESSION['shop_id'];
    debug_log('ショップID', $shop_id);
    
    if (!$shop_id) {
        throw new Exception('ショップIDがセッションに設定されていません');
    }
    
    // キャストテーブルの存在確認
    debug_log('5. キャストテーブル確認開始');
    $result = $db->fetch("SELECT COUNT(*) as count FROM casts WHERE shop_id = ?", [$shop_id]);
    debug_log('キャスト数', $result);
    
    // キャスト一覧取得
    debug_log('6. キャスト一覧取得開始');
    $casts = $db->fetchAll("SELECT * FROM casts WHERE shop_id = ?", [$shop_id]);
    debug_log('キャスト一覧取得完了', count($casts) . '件');
    
    debug_log('=== キャスト管理デバッグ完了（成功） ===');
    
    echo "<h2>成功！詳細はログファイルを確認してください。</h2>";
    echo "<p>ログファイル: $log_file</p>";
    
} catch (Exception $e) {
    debug_log('=== エラーが発生しました ===');
    debug_log('エラーメッセージ', $e->getMessage());
    debug_log('ファイル', $e->getFile());
    debug_log('行', $e->getLine());
    debug_log('スタックトレース', $e->getTraceAsString());
    
    echo "<h2>エラーが発生しました。ログファイルを確認してください。</h2>";
    echo "<p>ログファイル: $log_file</p>";
    echo "<p>エラーメッセージ: " . $e->getMessage() . "</p>";
}
?>
