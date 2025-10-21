<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>住所確認ページ デバッグ</h1>";

try {
    echo "<h2>1. 基本ファイル読み込みテスト</h2>";
    
    require_once 'includes.php';
    echo "<p>✅ includes.php loaded</p>";
    
    echo "<h2>2. セッション確認</h2>";
    session_start();
    echo "<p>✅ Session started</p>";
    
    echo "<h3>セッション内容:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h2>3. 店舗管理者認証チェック</h2>";
    
    if (is_shop_admin()) {
        echo "<p>✅ Shop admin authenticated</p>";
        
        $shop_id = get_shop_admin_shop_id();
        $shop_name = get_shop_admin_shop_name();
        
        echo "<p>Shop ID: " . $shop_id . "</p>";
        echo "<p>Shop Name: " . htmlspecialchars($shop_name) . "</p>";
        
        echo "<h2>4. 店舗ステータス確認</h2>";
        $shop_status = $_SESSION['shop_status'] ?? 'unknown';
        echo "<p>Shop Status: " . $shop_status . "</p>";
        
        if ($shop_status === 'verification_pending') {
            echo "<p>✅ Verification pending - should show verification page</p>";
        } else {
            echo "<p>⚠️ Shop status is not verification_pending</p>";
        }
        
        echo "<h2>5. 店舗情報取得テスト</h2>";
        $shop_info = $db->fetch("SELECT address, verification_code FROM shops WHERE id = ?", [$shop_id]);
        
        if ($shop_info) {
            echo "<p>✅ Shop info retrieved</p>";
            echo "<p>Address: " . htmlspecialchars($shop_info['address']) . "</p>";
            echo "<p>Verification Code: " . htmlspecialchars($shop_info['verification_code']) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Shop info not found</p>";
        }
        
        echo "<h2>6. POST処理テスト</h2>";
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
            echo "<p>POST data received</p>";
            
            $input_code = sanitize_input($_POST['verification_code']);
            echo "<p>Input code: " . htmlspecialchars($input_code) . "</p>";
            
            if (empty($input_code)) {
                echo "<p style='color: red;'>❌ Verification code is empty</p>";
            } elseif (strlen($input_code) !== 6 || !is_numeric($input_code)) {
                echo "<p style='color: red;'>❌ Verification code format invalid</p>";
            } else {
                echo "<p>✅ Verification code format valid</p>";
                
                if ($shop_info && $shop_info['verification_code'] === $input_code) {
                    echo "<p style='color: green;'>✅ Verification code matches!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Verification code does not match</p>";
                }
            }
        } else {
            echo "<p>POST data not received</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Shop admin not authenticated</p>";
        echo "<p>Redirecting to shop admin login...</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>デバッグ完了</h2>";
?>
