<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>shop_login.php 詳細デバッグ</h1>";

try {
    echo "<h2>1. 基本ファイル読み込み</h2>";
    
    require_once 'config/config.php';
    echo "<p>✅ config.php loaded</p>";
    
    require_once 'includes/functions.php';
    echo "<p>✅ functions.php loaded</p>";
    
    echo "<h2>2. セッション開始</h2>";
    session_start();
    echo "<p>✅ Session started</p>";
    
    echo "<h2>3. データベース接続テスト</h2>";
    if (isset($db)) {
        echo "<p>✅ Database object exists</p>";
        
        // テストクエリ
        $test_result = $db->fetch("SELECT 1 as test");
        if ($test_result) {
            echo "<p>✅ Database connection working</p>";
        } else {
            echo "<p>❌ Database query failed</p>";
        }
    } else {
        echo "<p>❌ Database object not found</p>";
    }
    
    echo "<h2>4. 関数テスト</h2>";
    if (function_exists('sanitize_input')) {
        echo "<p>✅ sanitize_input function exists</p>";
    } else {
        echo "<p>❌ sanitize_input function not found</p>";
    }
    
    if (function_exists('verify_password')) {
        echo "<p>✅ verify_password function exists</p>";
    } else {
        echo "<p>❌ verify_password function not found</p>";
    }
    
    echo "<h2>5. shop_adminsテーブル確認</h2>";
    $shop_admins = $db->fetchAll("SELECT id, email, username, status FROM shop_admins LIMIT 5");
    if ($shop_admins) {
        echo "<p>✅ shop_admins table has data</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Status</th></tr>";
        foreach ($shop_admins as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['id'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ shop_admins table is empty or doesn't exist</p>";
    }
    
    echo "<h2>6. shopsテーブル確認</h2>";
    $shops = $db->fetchAll("SELECT id, name, status, verification_code FROM shops LIMIT 5");
    if ($shops) {
        echo "<p>✅ shops table has data</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Verification Code</th></tr>";
        foreach ($shops as $shop) {
            echo "<tr>";
            echo "<td>" . $shop['id'] . "</td>";
            echo "<td>" . htmlspecialchars($shop['name']) . "</td>";
            echo "<td>" . htmlspecialchars($shop['status']) . "</td>";
            echo "<td>" . htmlspecialchars($shop['verification_code'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ shops table is empty or doesn't exist</p>";
    }
    
    echo "<h2>7. JOINクエリテスト</h2>";
    try {
        $join_test = $db->fetch(
            "SELECT sa.*, s.name as shop_name, s.status as shop_status, s.verification_code
             FROM shop_admins sa
             JOIN shops s ON sa.shop_id = s.id
             WHERE sa.email = ? AND sa.status = 'active'
             LIMIT 1",
            ['admin@example.com']
        );
        
        if ($join_test) {
            echo "<p>✅ JOIN query successful</p>";
            echo "<ul>";
            echo "<li>ID: " . $join_test['id'] . "</li>";
            echo "<li>Email: " . htmlspecialchars($join_test['email']) . "</li>";
            echo "<li>Username: " . htmlspecialchars($join_test['username']) . "</li>";
            echo "<li>Shop Name: " . htmlspecialchars($join_test['shop_name']) . "</li>";
            echo "<li>Shop Status: " . htmlspecialchars($join_test['shop_status']) . "</li>";
            echo "<li>Verification Code: " . htmlspecialchars($join_test['verification_code'] ?? 'NULL') . "</li>";
            echo "</ul>";
        } else {
            echo "<p>⚠️ JOIN query returned no results for admin@example.com</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ JOIN query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>8. shop_login.phpの内容確認</h2>";
    $shop_login_content = file_get_contents('pages/shop_login.php');
    echo "<p>ファイルサイズ: " . strlen($shop_login_content) . " bytes</p>";
    
    // 重要な部分を抜粋
    if (strpos($shop_login_content, 'verification_code') !== false) {
        echo "<p>✅ verification_code is included in SQL query</p>";
    } else {
        echo "<p>❌ verification_code not found in SQL query</p>";
    }
    
    if (strpos($shop_login_content, '$_SESSION[\'verification_code\']') !== false) {
        echo "<p>✅ verification_code is saved to session</p>";
    } else {
        echo "<p>❌ verification_code not saved to session</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>デバッグ完了</h2>";
?>
