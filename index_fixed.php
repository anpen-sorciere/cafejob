<?php
session_start();
require_once 'config/database.php';

// functions.phpの読み込みを安全に
if (file_exists('includes/functions.php')) {
    require_once 'includes/functions.php';
} else {
    // 簡易版の関数を定義
    function sanitize_input($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    function verify_password($password, $hash) {
        // 一時的に平文パスワードも対応
        if ($password === $hash) {
            return true;
        }
        return password_verify($password, $hash);
    }
    
    function is_logged_in() {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }
    
    function is_admin() {
        return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
    }
}

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

