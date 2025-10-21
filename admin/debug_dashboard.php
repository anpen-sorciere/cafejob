<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>システム管理者ダッシュボードデバッグ</h1>";

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>1. セッション情報</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>2. ファイル読み込みテスト</h2>";
try {
    require_once '../config/config.php';
    echo "✅ config.php読み込み正常<br>";
} catch (Exception $e) {
    echo "❌ config.php読み込みエラー: " . $e->getMessage() . "<br>";
}

try {
    require_once '../config/database.php';
    echo "✅ database.php読み込み正常<br>";
} catch (Exception $e) {
    echo "❌ database.php読み込みエラー: " . $e->getMessage() . "<br>";
}

try {
    require_once '../includes/functions.php';
    echo "✅ functions.php読み込み正常<br>";
} catch (Exception $e) {
    echo "❌ functions.php読み込みエラー: " . $e->getMessage() . "<br>";
}

echo "<h2>3. 管理者認証テスト</h2>";
if (function_exists('is_admin')) {
    $is_admin = is_admin();
    echo "is_admin()結果: " . ($is_admin ? 'true' : 'false') . "<br>";
    
    if ($is_admin) {
        echo "✅ 管理者として認証されています<br>";
    } else {
        echo "❌ 管理者として認証されていません<br>";
        
        // 詳細な認証チェック
        echo "<h3>詳細な認証チェック</h3>";
        echo "admin_id: " . ($_SESSION['admin_id'] ?? 'NOT SET') . "<br>";
        echo "admin_username: " . ($_SESSION['admin_username'] ?? 'NOT SET') . "<br>";
        echo "admin_role: " . ($_SESSION['admin_role'] ?? 'NOT SET') . "<br>";
    }
} else {
    echo "❌ is_admin()関数が存在しません<br>";
}

echo "<h2>4. データベース接続テスト</h2>";
try {
    $result = $db->fetch("SELECT COUNT(*) as count FROM users");
    echo "✅ データベース接続正常: ユーザー数 " . $result['count'] . "件<br>";
} catch (Exception $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>5. 統計データ取得テスト</h2>";
try {
    $total_users = $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'];
    echo "✅ 総ユーザー数: " . $total_users . "<br>";
} catch (Exception $e) {
    echo "❌ 総ユーザー数取得エラー: " . $e->getMessage() . "<br>";
}

try {
    $total_shops = $db->fetch("SELECT COUNT(*) as count FROM shops")['count'];
    echo "✅ 総店舗数: " . $total_shops . "<br>";
} catch (Exception $e) {
    echo "❌ 総店舗数取得エラー: " . $e->getMessage() . "<br>";
}

try {
    $total_jobs = $db->fetch("SELECT COUNT(*) as count FROM jobs")['count'];
    echo "✅ 総求人数: " . $total_jobs . "<br>";
} catch (Exception $e) {
    echo "❌ 総求人数取得エラー: " . $e->getMessage() . "<br>";
}

try {
    $total_applications = $db->fetch("SELECT COUNT(*) as count FROM applications")['count'];
    echo "✅ 総応募数: " . $total_applications . "<br>";
} catch (Exception $e) {
    echo "❌ 総応募数取得エラー: " . $e->getMessage() . "<br>";
}

try {
    $total_casts = $db->fetch("SELECT COUNT(*) as count FROM casts")['count'];
    echo "✅ 総キャスト数: " . $total_casts . "<br>";
} catch (Exception $e) {
    echo "❌ 総キャスト数取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>6. 最新データ取得テスト</h2>";
try {
    $recent_applications = $db->fetchAll(
        "SELECT a.*, j.title as job_title, s.name as shop_name, u.username, u.email
         FROM applications a
         JOIN jobs j ON a.job_id = j.id
         JOIN shops s ON j.shop_id = s.id
         JOIN users u ON a.user_id = u.id
         ORDER BY a.applied_at DESC
         LIMIT 5"
    );
    echo "✅ 最新応募データ取得正常: " . count($recent_applications) . "件<br>";
} catch (Exception $e) {
    echo "❌ 最新応募データ取得エラー: " . $e->getMessage() . "<br>";
}

try {
    $recent_shops = $db->fetchAll(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name
         FROM shops s
         LEFT JOIN prefectures p ON s.prefecture_id = p.id
         LEFT JOIN cities c ON s.city_id = c.id
         ORDER BY s.created_at DESC
         LIMIT 5"
    );
    echo "✅ 最新店舗データ取得正常: " . count($recent_shops) . "件<br>";
} catch (Exception $e) {
    echo "❌ 最新店舗データ取得エラー: " . $e->getMessage() . "<br>";
}

try {
    $recent_reviews = $db->fetchAll(
        "SELECT r.*, s.name as shop_name, u.username
         FROM reviews r
         JOIN shops s ON r.shop_id = s.id
         LEFT JOIN users u ON r.user_id = u.id
         ORDER BY r.created_at DESC
         LIMIT 5"
    );
    echo "✅ 最新口コミデータ取得正常: " . count($recent_reviews) . "件<br>";
} catch (Exception $e) {
    echo "❌ 最新口コミデータ取得エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>7. テスト完了</h2>";
echo "<p>このテスト結果を確認して、問題がある場合は修正してください。</p>";
?>
