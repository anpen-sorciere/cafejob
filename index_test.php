<?php
// シンプルなテスト用index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>カフェJob テストページ</h1>";

try {
    echo "<p>1. セッション開始...</p>";
    session_start();
    
    echo "<p>2. 設定ファイル読み込み...</p>";
    require_once 'config/config.php';
    
    echo "<p>3. データベース接続...</p>";
    require_once 'config/database.php';
    
    echo "<p>4. 関数ファイル読み込み...</p>";
    require_once 'includes/functions.php';
    
    echo "<p style='color: green;'>✓ すべてのファイルが正常に読み込まれました</p>";
    
    // 簡単なデータベーステスト
    echo "<h2>データベーステスト</h2>";
    $result = $db->fetch("SELECT COUNT(*) as count FROM jobs");
    echo "<p>求人テーブルのレコード数: " . $result['count'] . "</p>";
    
    $result = $db->fetch("SELECT COUNT(*) as count FROM shops");
    echo "<p>店舗テーブルのレコード数: " . $result['count'] . "</p>";
    
    echo "<h2>ページテスト</h2>";
    echo "<p><a href='?page=jobs'>求人ページ</a></p>";
    echo "<p><a href='?page=shops'>店舗ページ</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>✗ 致命的エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
}
?>
