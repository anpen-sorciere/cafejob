<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// ログアウト処理
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// メインページの表示
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'search', 'jobs', 'shops', 'cast', 'login', 'register', 'admin_login'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

include 'pages/' . $page . '.php';
?>
