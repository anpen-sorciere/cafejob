<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// 店舗管理者認証チェック
if (!is_shop_admin()) {
    header('Location: ../?page=shop_admin_login');
    exit;
}

// 住所確認が必要な場合は確認ページにリダイレクト
if ($_SESSION['shop_status'] === 'verification_pending') {
    header('Location: verify_address.php');
    exit;
}

// 住所変更がロックされている場合は確認ページにリダイレクト
$db = new Database();
$shop_id = $_SESSION['shop_id'];

$locked_address_change = $db->fetch(
    "SELECT id FROM shop_address_changes 
     WHERE shop_id = ? AND status = 'pending' AND is_locked = TRUE 
     ORDER BY created_at DESC LIMIT 1",
    [$shop_id]
);

if ($locked_address_change && basename($_SERVER['PHP_SELF']) !== 'verify_address.php') {
    $_SESSION['address_verification_pending'] = true;
    header('Location: verify_address.php');
    exit;
}
?>
