<?php
session_start();

// 店舗管理者セッションをクリア
unset($_SESSION['shop_admin_id']);
unset($_SESSION['shop_admin_email']);
unset($_SESSION['shop_id']);
unset($_SESSION['shop_name']);

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header('Location: ../?page=shop_admin_login');
exit;
?>
