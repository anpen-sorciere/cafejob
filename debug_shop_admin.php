<?php
// デバッグ用ファイル
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. PHP動作確認<br>";
echo "2. セッション開始...<br>";
session_start();
echo "3. 設定ファイル読み込み...<br>";
require_once 'config/config.php';
echo "4. 関数ファイル読み込み...<br>";
require_once 'includes/functions.php';
echo "5. データベース接続...<br>";
require_once 'config/database.php';
echo "6. 店舗管理者テーブル確認...<br>";

try {
    $result = $db->fetch("SELECT COUNT(*) as count FROM shop_admins");
    echo "shop_adminsテーブル: " . $result['count'] . "件<br>";
} catch (Exception $e) {
    echo "shop_adminsテーブルエラー: " . $e->getMessage() . "<br>";
}

echo "7. 店舗テーブル確認...<br>";
try {
    $result = $db->fetch("SELECT COUNT(*) as count FROM shops");
    echo "shopsテーブル: " . $result['count'] . "件<br>";
} catch (Exception $e) {
    echo "shopsテーブルエラー: " . $e->getMessage() . "<br>";
}

echo "8. 完了<br>";
?>
