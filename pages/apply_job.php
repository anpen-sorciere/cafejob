<?php
/**
 * 求人応募処理
 * 応募時にチャットルームを自動作成
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// ユーザー認証チェック
if (!is_logged_in()) {
    header('Location: ?page=login');
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = $_POST['job_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$job_id || empty($message)) {
    $_SESSION['error_message'] = '応募情報が不正です。';
    header('Location: ?page=jobs');
    exit;
}

$db = new Database();

try {
    // 求人情報を取得
    $job = $db->fetch("
        SELECT j.*, s.id as shop_id, s.name as shop_name
        FROM jobs j
        JOIN shops s ON j.shop_id = s.id
        WHERE j.id = ? AND j.status = 'active' AND s.status = 'active'
    ", [$job_id]);
    
    if (!$job) {
        $_SESSION['error_message'] = '求人が見つかりません。';
        header('Location: ?page=jobs');
        exit;
    }
    
    // 既に応募していないかチェック
    $existing_application = $db->fetch("
        SELECT id FROM applications 
        WHERE user_id = ? AND job_id = ?
    ", [$user_id, $job_id]);
    
    if ($existing_application) {
        $_SESSION['error_message'] = '既にこの求人に応募しています。';
        header('Location: ?page=jobs');
        exit;
    }
    
    // 応募をデータベースに保存
    $db->query("
        INSERT INTO applications (user_id, job_id, message, status, created_at)
        VALUES (?, ?, ?, 'pending', NOW())
    ", [$user_id, $job_id, $message]);
    
    $application_id = $db->getConnection()->lastInsertId();
    
    // チャットルームを作成
    $db->query("
        INSERT INTO chat_rooms (application_id, shop_id, user_id, status, created_at)
        VALUES (?, ?, ?, 'active', NOW())
    ", [$application_id, $job['shop_id'], $user_id]);
    
    $_SESSION['success_message'] = '応募が完了しました。チャットルームが作成されました。店舗からの連絡をお待ちください。';
    header('Location: ?page=jobs');
    exit;
    
} catch (Exception $e) {
    $_SESSION['error_message'] = '応募の処理中にエラーが発生しました。';
    header('Location: ?page=jobs');
    exit;
}
?>

