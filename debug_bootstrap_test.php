<?php
/**
 * bootstrap.php の動作テスト
 * どこからでも同じ方法でconfigファイルを読み込めるかテスト
 */

echo "<h1>bootstrap.php 動作テスト</h1>";

try {
    echo "<h2>1. bootstrap.php 読み込みテスト</h2>";
    require_once 'bootstrap.php';
    echo "<p style='color: green;'>bootstrap.php 読み込み成功</p>";
    
    echo "<h2>2. PROJECT_ROOT 定数確認</h2>";
    if (defined('PROJECT_ROOT')) {
        echo "<p style='color: green;'>PROJECT_ROOT: " . PROJECT_ROOT . "</p>";
    } else {
        echo "<p style='color: red;'>PROJECT_ROOT が定義されていません</p>";
    }
    
    echo "<h2>3. データベース接続テスト</h2>";
    $db = new Database();
    echo "<p style='color: green;'>データベース接続成功</p>";
    
    echo "<h2>4. 関数存在確認</h2>";
    $functions_to_check = ['is_shop_admin', 'sanitize_input', 'verify_password'];
    foreach ($functions_to_check as $func) {
        $exists = function_exists($func);
        $color = $exists ? 'green' : 'red';
        echo "<p style='color: {$color};'>{$func}: " . ($exists ? '存在' : '不存在') . "</p>";
    }
    
    echo "<h2>5. 現在のディレクトリ情報</h2>";
    echo "<p><strong>現在のディレクトリ:</strong> " . getcwd() . "</p>";
    echo "<p><strong>スクリプトのディレクトリ:</strong> " . __DIR__ . "</p>";
    echo "<p><strong>スクリプトファイル:</strong> " . __FILE__ . "</p>";
    
    echo "<h2>6. ファイル存在確認</h2>";
    $files_to_check = [
        PROJECT_ROOT . '/config/config.php',
        PROJECT_ROOT . '/config/database.php',
        PROJECT_ROOT . '/includes/functions.php'
    ];
    
    foreach ($files_to_check as $file) {
        $exists = file_exists($file);
        $color = $exists ? 'green' : 'red';
        echo "<p style='color: {$color};'>{$file}: " . ($exists ? '存在' : '不存在') . "</p>";
    }
    
    echo "<h2>7. セッション設定テスト</h2>";
    if (session_status() === PHP_SESSION_NONE) {
        session_name('cafejob_session');
        session_start();
        echo "<p style='color: green;'>セッション開始成功</p>";
        echo "<p><strong>セッション名:</strong> " . session_name() . "</p>";
        echo "<p><strong>セッションID:</strong> " . session_id() . "</p>";
    } else {
        echo "<p style='color: orange;'>セッション既に開始済み</p>";
    }
    
    echo "<h2>8. テスト完了</h2>";
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ bootstrap.php は正常に動作しています！</p>";
    
} catch (Exception $e) {
    echo "<h2>エラー発生</h2>";
    echo "<p style='color: red;'>エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: red;'>エラーファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p style='color: red;'>エラー行: " . $e->getLine() . "</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
?>
