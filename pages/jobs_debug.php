<?php
// デバッグ用のjobs.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- デバッグ開始 -->";

try {
    echo "<p>1. セッション開始...</p>";
    session_start();
    
    echo "<p>2. 設定ファイル読み込み...</p>";
    require_once 'config/config.php';
    
    echo "<p>3. データベース接続...</p>";
    require_once 'config/database.php';
    
    echo "<p>4. 関数ファイル読み込み...</p>";
    require_once 'includes/functions.php';
    
    echo "<p>5. ページタイトル設定...</p>";
    $page_title = '求人検索';
    $page_description = 'コンカフェの求人情報を検索できます。';
    
    echo "<p>6. 検索パラメータ取得...</p>";
    $keyword = isset($_GET['keyword']) ? sanitize_input($_GET['keyword']) : '';
    $prefecture_id = isset($_GET['prefecture']) ? (int)$_GET['prefecture'] : null;
    $concept_type = isset($_GET['concept_type']) ? sanitize_input($_GET['concept_type']) : null;
    $salary_min = isset($_GET['salary_min']) ? (int)$_GET['salary_min'] : null;
    $job_type = isset($_GET['job_type']) ? sanitize_input($_GET['job_type']) : null;
    $gender_requirement = isset($_GET['gender_requirement']) ? sanitize_input($_GET['gender_requirement']) : null;
    $sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'created_at';
    $page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    
    echo "<p>7. データベースクエリ実行...</p>";
    
    // 簡単なクエリでテスト
    $total_jobs = $db->fetch("SELECT COUNT(*) as total FROM jobs WHERE status = 'active'")['total'];
    echo "<p>総求人数: {$total_jobs}</p>";
    
    $jobs = $db->fetchAll("SELECT j.*, s.name as shop_name FROM jobs j JOIN shops s ON j.shop_id = s.id WHERE j.status = 'active' LIMIT 5");
    echo "<p>取得した求人数: " . count($jobs) . "</p>";
    
    $prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id LIMIT 5");
    echo "<p>都道府県数: " . count($prefectures) . "</p>";
    
    echo "<p style='color: green;'>✓ すべての処理が正常に完了しました</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ エラー発生:</p>";
    echo "<p>メッセージ: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Error $e) {
    echo "<p style='color: red;'>✗ 致命的エラー発生:</p>";
    echo "<p>メッセージ: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<!-- デバッグ終了 -->";
?>
