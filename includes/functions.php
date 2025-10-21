<?php
// 管理者パネルからのアクセスかどうかを判定してパスを調整
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/shop_admin/') !== false) {
    require_once '../config/config.php';
    require_once '../config/database.php';
} else {
    require_once 'config/config.php';
    require_once 'config/database.php';
}

// セキュリティ関数
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    // 一時的に平文パスワードも対応
    if ($password === $hash) {
        return true;
    }
    return password_verify($password, $hash);
}

// セッション管理
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function is_admin() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ?page=login');
        exit;
    }
}

// データベース操作関数
function get_shop($id) {
    global $db;
    return $db->fetch(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name 
         FROM shops s 
         LEFT JOIN prefectures p ON s.prefecture_id = p.id 
         LEFT JOIN cities c ON s.city_id = c.id 
         WHERE s.id = ? AND s.status = 'active'",
        [$id]
    );
}

function get_jobs($shop_id = null, $limit = null, $offset = 0) {
    global $db;
    $sql = "SELECT j.*, s.name as shop_name, s.address, p.name as prefecture_name, c.name as city_name
            FROM jobs j
            JOIN shops s ON j.shop_id = s.id
            LEFT JOIN prefectures p ON s.prefecture_id = p.id
            LEFT JOIN cities c ON s.city_id = c.id
            WHERE j.status = 'active'";
    
    $params = [];
    if ($shop_id) {
        $sql .= " AND j.shop_id = ?";
        $params[] = $shop_id;
    }
    
    $sql .= " ORDER BY j.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    return $db->fetchAll($sql, $params);
}

function get_casts($shop_id) {
    global $db;
    return $db->fetchAll(
        "SELECT * FROM casts WHERE shop_id = ? AND status = 'active' ORDER BY created_at DESC",
        [$shop_id]
    );
}

function search_shops($keyword = '', $prefecture_id = null, $concept_type = null, $limit = 20, $offset = 0) {
    global $db;
    
    $sql = "SELECT s.*, p.name as prefecture_name, c.name as city_name
            FROM shops s
            LEFT JOIN prefectures p ON s.prefecture_id = p.id
            LEFT JOIN cities c ON s.city_id = c.id
            WHERE s.status = 'active'";
    
    $params = [];
    
    if ($keyword) {
        $sql .= " AND (s.name LIKE ? OR s.description LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    
    if ($prefecture_id) {
        $sql .= " AND s.prefecture_id = ?";
        $params[] = $prefecture_id;
    }
    
    if ($concept_type) {
        $sql .= " AND s.concept_type = ?";
        $params[] = $concept_type;
    }
    
    $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->fetchAll($sql, $params);
}

// ファイルアップロード関数
function upload_file($file, $directory = 'uploads/') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return false;
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return false;
        default:
            return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime, $allowed_types)) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $directory . $filename;
    
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    return false;
}

// ページネーション関数
function paginate($total_items, $items_per_page, $current_page) {
    $total_pages = ceil($total_items / $items_per_page);
    $offset = ($current_page - 1) * $items_per_page;
    
    return [
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'items_per_page' => $items_per_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'prev_page' => $current_page > 1 ? $current_page - 1 : null,
        'next_page' => $current_page < $total_pages ? $current_page + 1 : null
    ];
}

// 日付フォーマット関数
function format_date($date, $format = 'Y年m月d日') {
    return date($format, strtotime($date));
}

function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'たった今';
    if ($time < 3600) return floor($time/60) . '分前';
    if ($time < 86400) return floor($time/3600) . '時間前';
    if ($time < 2592000) return floor($time/86400) . '日前';
    if ($time < 31536000) return floor($time/2592000) . 'ヶ月前';
    
    return floor($time/31536000) . '年前';
}

// エラーメッセージ表示
function display_error($message) {
    return '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}

function display_success($message) {
    return '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

// 店舗管理者認証関数
function is_shop_admin() {
    return isset($_SESSION['shop_admin_id']) && !empty($_SESSION['shop_admin_id']);
}

function require_shop_admin() {
    error_log('require_shop_admin() called');
    error_log('is_shop_admin() result: ' . (is_shop_admin() ? 'true' : 'false'));
    error_log('Session shop_admin_id: ' . ($_SESSION['shop_admin_id'] ?? 'NOT SET'));
    
    if (!is_shop_admin()) {
        error_log('Shop admin not authenticated, redirecting to shop_login');
        header('Location: ../?page=shop_login');
        exit;
    }
    error_log('Shop admin authenticated successfully');
}

function get_shop_admin_shop_id() {
    return $_SESSION['shop_id'] ?? null;
}

function get_shop_admin_shop_name() {
    return $_SESSION['shop_name'] ?? null;
}
?>
