<?php
// 基本的なPHP動作確認
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP動作確認開始<br>";

try {
    echo "1. セッション開始...<br>";
    session_start();
    echo "2. 設定ファイル読み込み...<br>";
    require_once 'config/config.php';
    echo "3. 関数ファイル読み込み...<br>";
    require_once 'includes/functions.php';
    echo "4. データベース接続...<br>";
    require_once 'config/database.php';
    echo "5. 管理者テーブル確認...<br>";
    
    $result = $db->fetch("SELECT COUNT(*) as count FROM admins");
    echo "adminsテーブル: " . $result['count'] . "件<br>";
    
    echo "6. 管理者ログインページの読み込みテスト...<br>";
    ob_start();
    include 'pages/admin_login.php';
    $content = ob_get_clean();
    echo "管理者ログインページ読み込み成功<br>";
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "<br>";
    echo "ファイル: " . $e->getFile() . "<br>";
    echo "行: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "Fatal Error: " . $e->getMessage() . "<br>";
    echo "ファイル: " . $e->getFile() . "<br>";
    echo "行: " . $e->getLine() . "<br>";
}

echo "デバッグ完了<br>";
?>
