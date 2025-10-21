<?php
session_start();

// デバッグモードを有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>キャスト管理デバッグ</h1>";

try {
    echo "<p>1. セッション開始完了</p>";
    
    // 設定ファイル読み込み
    require_once 'includes.php';
    echo "<p>2. 設定ファイル読み込み完了</p>";
    
    // 認証チェック
    if (!is_shop_admin()) {
        echo "<p>エラー: 店舗管理者として認証されていません</p>";
        echo "<p>セッション情報: " . print_r($_SESSION, true) . "</p>";
        exit;
    }
    echo "<p>3. 認証チェック完了</p>";
    
    // データベース接続テスト
    $shop_id = $_SESSION['shop_id'];
    echo "<p>4. ショップID: " . $shop_id . "</p>";
    
    // キャストテーブルの存在確認
    $result = $db->fetch("SELECT COUNT(*) as count FROM casts WHERE shop_id = ?", [$shop_id]);
    echo "<p>5. キャスト数: " . $result['count'] . "</p>";
    
    // キャスト一覧取得
    $casts = $db->fetchAll("SELECT * FROM casts WHERE shop_id = ?", [$shop_id]);
    echo "<p>6. キャスト一覧取得完了: " . count($casts) . "件</p>";
    
    echo "<h2>成功！キャスト管理ページが正常に動作しています。</h2>";
    
} catch (Exception $e) {
    echo "<h2>エラーが発生しました:</h2>";
    echo "<p>エラーメッセージ: " . $e->getMessage() . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<p>スタックトレース:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
