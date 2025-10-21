<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>システム管理者ダッシュボード詳細デバッグ</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ セッション開始完了<br>";

    echo "<h2>2. ファイル読み込み</h2>";
    require_once '../config/config.php';
    echo "✅ config.php読み込み完了<br>";
    
    require_once '../config/database.php';
    echo "✅ database.php読み込み完了<br>";
    
    require_once '../includes/functions.php';
    echo "✅ functions.php読み込み完了<br>";

    echo "<h2>3. 管理者認証チェック</h2>";
    if (!function_exists('is_admin')) {
        throw new Exception('is_admin()関数が存在しません');
    }
    
    $is_admin = is_admin();
    echo "is_admin()結果: " . ($is_admin ? 'true' : 'false') . "<br>";
    
    if (!$is_admin) {
        echo "❌ 管理者として認証されていません<br>";
        echo "セッション情報:<br>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        throw new Exception('管理者として認証されていません');
    }
    echo "✅ 管理者認証完了<br>";

    echo "<h2>4. 変数設定</h2>";
    $page_title = '管理者ダッシュボード';
    $admin_role = $_SESSION['admin_role'] ?? 'unknown';
    echo "✅ 変数設定完了: page_title='$page_title', admin_role='$admin_role'<br>";

    echo "<h2>5. 統計データ取得</h2>";
    $stats = [
        'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'],
        'total_shops' => $db->fetch("SELECT COUNT(*) as count FROM shops")['count'],
        'total_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs")['count'],
        'total_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications")['count'],
        'total_casts' => $db->fetch("SELECT COUNT(*) as count FROM casts")['count'],
        'pending_shops' => $db->fetch("SELECT COUNT(*) as count FROM shops WHERE status IN ('pending', 'verification_pending')")['count'],
        'pending_reviews' => $db->fetch("SELECT COUNT(*) as count FROM reviews WHERE status = 'pending'")['count']
    ];
    echo "✅ 統計データ取得完了<br>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";

    echo "<h2>6. 最新データ取得</h2>";
    $recent_applications = $db->fetchAll(
        "SELECT a.*, j.title as job_title, s.name as shop_name, u.username, u.email
         FROM applications a
         JOIN jobs j ON a.job_id = j.id
         JOIN shops s ON j.shop_id = s.id
         JOIN users u ON a.user_id = u.id
         ORDER BY a.applied_at DESC
         LIMIT 10"
    );
    echo "✅ 最新応募データ取得完了: " . count($recent_applications) . "件<br>";

    $recent_shops = $db->fetchAll(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name
         FROM shops s
         LEFT JOIN prefectures p ON s.prefecture_id = p.id
         LEFT JOIN cities c ON s.city_id = c.id
         ORDER BY s.created_at DESC
         LIMIT 10"
    );
    echo "✅ 最新店舗データ取得完了: " . count($recent_shops) . "件<br>";

    $recent_reviews = $db->fetchAll(
        "SELECT r.*, s.name as shop_name, u.username
         FROM reviews r
         JOIN shops s ON r.shop_id = s.id
         LEFT JOIN users u ON r.user_id = u.id
         ORDER BY r.created_at DESC
         LIMIT 10"
    );
    echo "✅ 最新口コミデータ取得完了: " . count($recent_reviews) . "件<br>";

    echo "<h2>7. time_ago関数定義</h2>";
    function time_ago($datetime) {
        $time = time() - strtotime($datetime);
        if ($time < 60) return 'たった今';
        if ($time < 3600) return floor($time/60) . '分前';
        if ($time < 86400) return floor($time/3600) . '時間前';
        if ($time < 2592000) return floor($time/86400) . '日前';
        if ($time < 31536000) return floor($time/2592000) . 'ヶ月前';
        return floor($time/31536000) . '年前';
    }
    echo "✅ time_ago関数定義完了<br>";

    echo "<h2>8. HTML出力テスト</h2>";
    ob_start();
    echo "<!DOCTYPE html><html><head><title>Test</title></head><body><h1>Test</h1></body></html>";
    $html = ob_get_clean();
    echo "✅ HTML出力テスト完了<br>";

    echo "<h2>9. 成功！</h2>";
    echo "<p>すべてのテストが成功しました。問題は認証にあります。</p>";
    echo "<p><a href='clear_session.php'>セッションをクリア</a></p>";
    echo "<p><a href='../?page=admin_login'>システム管理者ログイン</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ エラーが発生しました</h2>";
    echo "<p>エラーメッセージ: " . $e->getMessage() . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<p>スタックトレース:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
