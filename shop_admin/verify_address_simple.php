<?php
// 最小限のverify_address.php
echo "Starting verify_address.php<br>";

try {
    session_start();
    echo "Session started<br>";
    
    // ファイル読み込みテスト
    if (file_exists('../includes/error_logger.php')) {
        require_once '../includes/error_logger.php';
        echo "error_logger.php loaded<br>";
        
        custom_error_log('verify_address.php - Starting execution');
        echo "Custom error log function called<br>";
    } else {
        echo "error_logger.php not found<br>";
    }
    
    if (file_exists('../config/config.php')) {
        require_once '../config/config.php';
        echo "config.php loaded<br>";
    } else {
        echo "config.php not found<br>";
    }
    
    if (file_exists('../includes/functions.php')) {
        require_once '../includes/functions.php';
        echo "functions.php loaded<br>";
    } else {
        echo "functions.php not found<br>";
    }
    
    // セッション情報表示
    echo "Session data:<br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    // 認証チェック
    if (function_exists('is_shop_admin')) {
        $is_admin = is_shop_admin();
        echo "is_shop_admin() result: " . ($is_admin ? 'true' : 'false') . "<br>";
        
        if (!$is_admin) {
            echo "Shop admin not authenticated<br>";
            echo "Redirecting to shop_login...<br>";
            header('Location: ../?page=shop_login');
            exit;
        } else {
            echo "Shop admin authenticated<br>";
        }
    } else {
        echo "is_shop_admin function not found<br>";
    }
    
    echo "verify_address.php completed successfully<br>";
    
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: " . $e->getTraceAsString() . "<br>";
} catch (Error $e) {
    echo "Error caught: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: " . $e->getTraceAsString() . "<br>";
}
?>
