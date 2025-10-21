<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ログファイル設定
$log_file = '../logs/cast_debug_detailed.log';
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

debug_log('=== キャスト管理詳細デバッグ開始 ===');

try {
    // 1. セッション開始
    debug_log('1. セッション開始');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    debug_log('セッション開始完了', session_id());
    
    // 2. 設定ファイル読み込み
    debug_log('2. 設定ファイル読み込み開始');
    require_once '../config/config.php';
    debug_log('config.php読み込み完了');
    
    // 3. データベース接続
    debug_log('3. データベース接続開始');
    require_once '../config/database.php';
    debug_log('database.php読み込み完了');
    
    // 4. 関数ファイル読み込み
    debug_log('4. 関数ファイル読み込み開始');
    require_once '../includes/functions.php';
    debug_log('functions.php読み込み完了');
    
    // 5. 認証チェック
    debug_log('5. 認証チェック開始');
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
    debug_log('認証チェック完了');
    
    // 6. ショップID取得
    debug_log('6. ショップID取得');
    $shop_id = $_SESSION['shop_id'] ?? null;
    debug_log('ショップID', $shop_id);
    
    if (!$shop_id) {
        throw new Exception('ショップIDがセッションに設定されていません');
    }
    
    // 7. キャストテーブル確認
    debug_log('7. キャストテーブル確認');
    $result = $db->fetch("SELECT COUNT(*) as count FROM casts WHERE shop_id = ?", [$shop_id]);
    debug_log('キャスト数', $result);
    
    // 8. キャスト一覧取得
    debug_log('8. キャスト一覧取得');
    $casts = $db->fetchAll("SELECT * FROM casts WHERE shop_id = ?", [$shop_id]);
    debug_log('キャスト一覧取得完了', count($casts) . '件');
    
    // 9. 成功メッセージ
    debug_log('=== キャスト管理詳細デバッグ完了（成功） ===');
    
    echo "<h2>✅ 成功！詳細はログファイルを確認してください。</h2>";
    echo "<p>ログファイル: $log_file</p>";
    echo "<p>キャスト数: " . count($casts) . "件</p>";
    
} catch (Exception $e) {
    debug_log('=== エラーが発生しました ===');
    debug_log('エラーメッセージ', $e->getMessage());
    debug_log('ファイル', $e->getFile());
    debug_log('行', $e->getLine());
    debug_log('スタックトレース', $e->getTraceAsString());
    
    echo "<h2>❌ エラーが発生しました。ログファイルを確認してください。</h2>";
    echo "<p>ログファイル: $log_file</p>";
    echo "<p>エラーメッセージ: " . $e->getMessage() . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
}
?>
