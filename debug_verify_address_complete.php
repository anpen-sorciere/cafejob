<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>verify_address.php 完全精査デバッグ</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    session_start();
    echo "<p>✅ Session started</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
    
    echo "<h2>2. セッション内容確認</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h2>3. ファイル読み込みテスト</h2>";
    
    echo "<p>config.php読み込み...</p>";
    require_once '../config/config.php';
    echo "<p>✅ config.php loaded</p>";
    
    echo "<p>functions.php読み込み...</p>";
    require_once '../includes/functions.php';
    echo "<p>✅ functions.php loaded</p>";
    
    echo "<h2>4. データベース接続確認</h2>";
    if (isset($db)) {
        echo "<p>✅ Database object exists</p>";
        $test = $db->fetch("SELECT 1 as test");
        if ($test) {
            echo "<p>✅ Database connection working</p>";
        } else {
            echo "<p>❌ Database query failed</p>";
        }
    } else {
        echo "<p>❌ Database object not found</p>";
    }
    
    echo "<h2>5. 関数存在確認</h2>";
    $functions_to_check = [
        'is_shop_admin',
        'require_shop_admin', 
        'get_shop_admin_shop_id',
        'get_shop_admin_shop_name',
        'sanitize_input'
    ];
    
    foreach ($functions_to_check as $func) {
        if (function_exists($func)) {
            echo "<p>✅ $func function exists</p>";
        } else {
            echo "<p>❌ $func function not found</p>";
        }
    }
    
    echo "<h2>6. is_shop_admin() テスト</h2>";
    if (function_exists('is_shop_admin')) {
        $is_admin = is_shop_admin();
        echo "<p>is_shop_admin() result: " . ($is_admin ? 'TRUE' : 'FALSE') . "</p>";
        
        if (!$is_admin) {
            echo "<p style='color: red;'>❌ Shop admin not authenticated</p>";
            echo "<p>Session shop_admin_id: " . ($_SESSION['shop_admin_id'] ?? 'NOT SET') . "</p>";
            echo "<p>Session shop_admin_id empty check: " . (empty($_SESSION['shop_admin_id']) ? 'EMPTY' : 'NOT EMPTY') . "</p>";
        } else {
            echo "<p style='color: green;'>✅ Shop admin authenticated</p>";
        }
    }
    
    echo "<h2>7. require_shop_admin() テスト</h2>";
    if (function_exists('require_shop_admin')) {
        echo "<p>require_shop_admin() を実行します...</p>";
        // この関数は認証に失敗するとリダイレクトするので、実行前に状態を確認
        if (!is_shop_admin()) {
            echo "<p style='color: red;'>❌ require_shop_admin() はリダイレクトを実行します</p>";
            echo "<p>リダイレクト先: ../?page=shop_login</p>";
        } else {
            echo "<p style='color: green;'>✅ require_shop_admin() は正常に通過します</p>";
        }
    }
    
    echo "<h2>8. セッション変数詳細確認</h2>";
    $required_session_vars = [
        'shop_admin_id',
        'shop_admin_email', 
        'shop_admin_username',
        'shop_id',
        'shop_name',
        'shop_status',
        'verification_code'
    ];
    
    foreach ($required_session_vars as $var) {
        if (isset($_SESSION[$var])) {
            echo "<p>✅ \$_SESSION['$var'] = " . htmlspecialchars($_SESSION[$var]) . "</p>";
        } else {
            echo "<p>❌ \$_SESSION['$var'] is NOT SET</p>";
        }
    }
    
    echo "<h2>9. shop_status チェック</h2>";
    if (isset($_SESSION['shop_status'])) {
        echo "<p>Current shop_status: " . htmlspecialchars($_SESSION['shop_status']) . "</p>";
        if ($_SESSION['shop_status'] !== 'verification_pending') {
            echo "<p style='color: orange;'>⚠️ shop_status is not 'verification_pending' - will redirect to dashboard</p>";
        } else {
            echo "<p style='color: green;'>✅ shop_status is 'verification_pending' - will stay on verify page</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ shop_status is NOT SET</p>";
    }
    
    echo "<h2>10. get_shop_admin_shop_id() テスト</h2>";
    if (function_exists('get_shop_admin_shop_id')) {
        $shop_id = get_shop_admin_shop_id();
        echo "<p>get_shop_admin_shop_id() result: " . ($shop_id ?? 'NULL') . "</p>";
    }
    
    echo "<h2>11. get_shop_admin_shop_name() テスト</h2>";
    if (function_exists('get_shop_admin_shop_name')) {
        $shop_name = get_shop_admin_shop_name();
        echo "<p>get_shop_admin_shop_name() result: " . ($shop_name ?? 'NULL') . "</p>";
    }
    
    echo "<h2>12. データベースクエリテスト</h2>";
    if (isset($shop_id) && $shop_id) {
        try {
            $shop_info = $db->fetch("SELECT address, verification_code FROM shops WHERE id = ?", [$shop_id]);
            if ($shop_info) {
                echo "<p>✅ Shop info query successful</p>";
                echo "<p>Address: " . htmlspecialchars($shop_info['address']) . "</p>";
                echo "<p>Verification Code: " . htmlspecialchars($shop_info['verification_code'] ?? 'NULL') . "</p>";
            } else {
                echo "<p>❌ Shop info query returned no results</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Shop info query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>⚠️ Cannot test shop info query - shop_id is null</p>";
    }
    
    echo "<h2>13. verify_address.php ファイル内容確認</h2>";
    $file_path = 'verify_address.php';
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        echo "<p>✅ File exists, size: " . strlen($content) . " bytes</p>";
        
        // 重要な部分をチェック
        $checks = [
            'session_start()' => strpos($content, 'session_start()') !== false,
            'require_once \'../config/config.php\'' => strpos($content, 'require_once \'../config/config.php\'') !== false,
            'require_once \'../includes/functions.php\'' => strpos($content, 'require_once \'../includes/functions.php\'') !== false,
            'require_shop_admin()' => strpos($content, 'require_shop_admin()') !== false,
            '$_SESSION[\'shop_status\']' => strpos($content, '$_SESSION[\'shop_status\']') !== false,
            'get_shop_admin_shop_id()' => strpos($content, 'get_shop_admin_shop_id()') !== false,
            'get_shop_admin_shop_name()' => strpos($content, 'get_shop_admin_shop_name()') !== false
        ];
        
        foreach ($checks as $check => $result) {
            echo "<p>" . ($result ? "✅" : "❌") . " $check</p>";
        }
    } else {
        echo "<p>❌ verify_address.php file not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>デバッグ完了</h2>";
?>
