<?php
// 管理者パネルからのアクセスかどうかを判定してパスを調整
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    require_once '../config/database.php';
} else {
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

function require_admin() {
    if (!is_admin()) {
        header('Location: ?page=login');
        exit;
    }
}

// データベース操作関数
function get_user_by_id($id) {
    global $db;
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
}

function get_user_by_username($username) {
    global $db;
    return $db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
}

function get_shop_by_id($id) {
    global $db;
    return $db->fetch("SELECT * FROM shops WHERE id = ?", [$id]);
}

function get_job_by_id($id) {
    global $db;
    return $db->fetch("SELECT * FROM jobs WHERE id = ?", [$id]);
}

// 検索関数
function search_jobs($keyword = '', $prefecture_id = null, $city_id = null, $job_type = null, $limit = 20, $offset = 0) {
    global $db;
    
    $sql = "SELECT j.*, s.name as shop_name, s.address, p.name as prefecture_name, c.name as city_name
            FROM jobs j
            JOIN shops s ON j.shop_id = s.id
            LEFT JOIN prefectures p ON s.prefecture_id = p.id
            LEFT JOIN cities c ON s.city_id = c.id
            WHERE j.status = 'active' AND s.status = 'active'";
    
    $params = [];
    
    if (!empty($keyword)) {
        $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR s.name LIKE ?)";
        $keyword_param = "%$keyword%";
        $params[] = $keyword_param;
        $params[] = $keyword_param;
        $params[] = $keyword_param;
    }
    
    if ($prefecture_id) {
        $sql .= " AND s.prefecture_id = ?";
        $params[] = $prefecture_id;
    }
    
    if ($city_id) {
        $sql .= " AND s.city_id = ?";
        $params[] = $city_id;
    }
    
    if ($job_type) {
        $sql .= " AND j.job_type = ?";
        $params[] = $job_type;
    }
    
    $sql .= " ORDER BY j.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->fetchAll($sql, $params);
}

function search_shops($keyword = '', $prefecture_id = null, $city_id = null, $concept_type = null, $limit = 20, $offset = 0) {
    global $db;
    
    $sql = "SELECT s.*, p.name as prefecture_name, c.name as city_name
            FROM shops s
            LEFT JOIN prefectures p ON s.prefecture_id = p.id
            LEFT JOIN cities c ON s.city_id = c.id
            WHERE s.status = 'active'";
    
    $params = [];
    
    if (!empty($keyword)) {
        $sql .= " AND (s.name LIKE ? OR s.description LIKE ? OR s.address LIKE ?)";
        $keyword_param = "%$keyword%";
        $params[] = $keyword_param;
        $params[] = $keyword_param;
        $params[] = $keyword_param;
    }
    
    if ($prefecture_id) {
        $sql .= " AND s.prefecture_id = ?";
        $params[] = $prefecture_id;
    }
    
    if ($city_id) {
        $sql .= " AND s.city_id = ?";
        $params[] = $city_id;
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

// ユーティリティ関数
function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'たった今';
    } elseif ($time < 3600) {
        return floor($time / 60) . '分前';
    } elseif ($time < 86400) {
        return floor($time / 3600) . '時間前';
    } elseif ($time < 2592000) {
        return floor($time / 86400) . '日前';
    } else {
        return date('Y年m月d日', strtotime($datetime));
    }
}

function format_salary($min, $max) {
    if ($min && $max) {
        return number_format($min) . '円～' . number_format($max) . '円';
    } elseif ($min) {
        return number_format($min) . '円～';
    } elseif ($max) {
        return '～' . number_format($max) . '円';
    } else {
        return '要相談';
    }
}

function get_prefectures() {
    global $db;
    return $db->fetchAll("SELECT * FROM prefectures ORDER BY id");
}

function get_cities($prefecture_id) {
    global $db;
    return $db->fetchAll("SELECT * FROM cities WHERE prefecture_id = ? ORDER BY name", [$prefecture_id]);
}

// ファイルアップロード関数
function upload_file($file, $directory = 'uploads') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return false;
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return false;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return false;
        default:
            return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    $allowed_types = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf'
    ];
    
    if (!in_array($mime_type, $allowed_types)) {
        return false;
    }
    
    $extension = array_search($mime_type, $allowed_types);
    $filename = sprintf('%s.%s', sha1_file($file['tmp_name']), $extension);
    $filepath = $directory . '/' . $filename;
    
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return false;
    }
    
    return $filepath;
}

// ログ関数
function write_log($message, $level = 'INFO') {
    if (!LOG_ENABLED) {
        return;
    }
    
    $log_file = LOG_FILE;
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// エラーハンドリング
function handle_error($message, $file = '', $line = 0) {
    write_log("ERROR: $message in $file on line $line", 'ERROR');
    
    if (DEBUG_MODE) {
        echo "<div style='color: red; background: #ffe6e6; padding: 10px; margin: 10px; border: 1px solid red;'>";
        echo "<strong>Error:</strong> $message<br>";
        echo "<strong>File:</strong> $file<br>";
        echo "<strong>Line:</strong> $line";
        echo "</div>";
    }
}

// デバッグ関数
function debug($var, $label = '') {
    if (DEBUG_MODE) {
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
        if ($label) {
            echo "<strong>$label:</strong><br>";
        }
        echo "<pre>" . print_r($var, true) . "</pre>";
        echo "</div>";
    }
}
?>

