<?php
// 店舗ログアウト処理
session_start();

// 店舗管理者セッションを削除
unset($_SESSION['shop_admin_id']);
unset($_SESSION['shop_admin_username']);
unset($_SESSION['shop_id']);
unset($_SESSION['shop_name']);

$_SESSION['success_message'] = 'ログアウトしました。';
header('Location: ?page=shop_login');
exit;
?>
