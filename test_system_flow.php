<?php
// システムフローテスト用スクリプト
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>カフェJobシステムフローテスト</h1>";

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ファイル読み込み
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

echo "<h2>1. データベース接続テスト</h2>";
try {
    $result = $db->fetch("SELECT COUNT(*) as count FROM shops");
    echo "✅ データベース接続正常: 店舗数 " . $result['count'] . "件<br>";
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>2. テーブル存在確認</h2>";
$tables = ['shops', 'shop_admins', 'users', 'jobs', 'applications', 'casts', 'reviews'];
foreach ($tables as $table) {
    try {
        $result = $db->fetch("SELECT COUNT(*) as count FROM $table");
        echo "✅ $table テーブル: " . $result['count'] . "件<br>";
    } catch (Exception $e) {
        echo "❌ $table テーブルエラー: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>3. 店舗管理者認証テスト</h2>";
// テスト用の店舗管理者データを取得
try {
    $shop_admin = $db->fetch("SELECT * FROM shop_admins LIMIT 1");
    if ($shop_admin) {
        echo "✅ 店舗管理者データ存在: " . $shop_admin['username'] . "<br>";
        echo "メール: " . $shop_admin['email'] . "<br>";
        echo "ショップID: " . $shop_admin['shop_id'] . "<br>";
    } else {
        echo "❌ 店舗管理者データが存在しません<br>";
    }
} catch (Exception $e) {
    echo "❌ 店舗管理者データ取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>4. ユーザーデータテスト</h2>";
try {
    $user = $db->fetch("SELECT * FROM users LIMIT 1");
    if ($user) {
        echo "✅ ユーザーデータ存在: " . $user['username'] . "<br>";
        echo "メール: " . $user['email'] . "<br>";
    } else {
        echo "❌ ユーザーデータが存在しません<br>";
    }
} catch (Exception $e) {
    echo "❌ ユーザーデータ取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>5. 求人データテスト</h2>";
try {
    $job = $db->fetch("SELECT * FROM jobs LIMIT 1");
    if ($job) {
        echo "✅ 求人データ存在: " . $job['title'] . "<br>";
        echo "ショップID: " . $job['shop_id'] . "<br>";
        echo "ステータス: " . $job['status'] . "<br>";
    } else {
        echo "❌ 求人データが存在しません<br>";
    }
} catch (Exception $e) {
    echo "❌ 求人データ取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>6. キャストデータテスト</h2>";
try {
    $cast = $db->fetch("SELECT * FROM casts LIMIT 1");
    if ($cast) {
        echo "✅ キャストデータ存在: " . $cast['name'] . "<br>";
        echo "ショップID: " . $cast['shop_id'] . "<br>";
        echo "ステータス: " . $cast['status'] . "<br>";
    } else {
        echo "❌ キャストデータが存在しません<br>";
    }
} catch (Exception $e) {
    echo "❌ キャストデータ取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>7. ファイル存在確認</h2>";
$files = [
    'pages/shop_register.php',
    'pages/shop_login.php',
    'shop_admin/dashboard.php',
    'shop_admin/job_create.php',
    'shop_admin/cast_management.php',
    'pages/register.php',
    'pages/login.php',
    'pages/jobs.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file 存在<br>";
    } else {
        echo "❌ $file 不存在<br>";
    }
}

echo "<h2>8. テスト完了</h2>";
echo "<p>このテスト結果を確認して、問題がある場合は修正してください。</p>";
echo "<p><a href='?page=shop_register'>店舗登録テスト</a></p>";
echo "<p><a href='?page=shop_login'>店舗ログインテスト</a></p>";
echo "<p><a href='?page=register'>ユーザー登録テスト</a></p>";
echo "<p><a href='?page=login'>ユーザーログインテスト</a></p>";
?>

