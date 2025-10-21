<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>shop_login.php 完全精査デバッグ</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    session_start();
    echo "<p>✅ Session started</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
    
    echo "<h2>2. 現在のセッション内容</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h2>3. POSTデータ確認</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>4. ファイル読み込みテスト</h2>";
    
    echo "<p>config.php読み込み...</p>";
    require_once 'config/config.php';
    echo "<p>✅ config.php loaded</p>";
    
    echo "<p>functions.php読み込み...</p>";
    require_once 'includes/functions.php';
    echo "<p>✅ functions.php loaded</p>";
    
    echo "<h2>5. データベース接続確認</h2>";
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
    
    echo "<h2>6. 関数存在確認</h2>";
    $functions_to_check = [
        'sanitize_input',
        'verify_password'
    ];
    
    foreach ($functions_to_check as $func) {
        if (function_exists($func)) {
            echo "<p>✅ $func function exists</p>";
        } else {
            echo "<p>❌ $func function not found</p>";
        }
    }
    
    echo "<h2>7. ログイン処理シミュレーション</h2>";
    
    // テスト用のPOSTデータをシミュレート
    $_POST = [
        'login' => '1',
        'email' => 'admin@example.com',
        'password' => 'password123'
    ];
    
    echo "<p>テスト用POSTデータを設定しました</p>";
    
    if ($_POST && isset($_POST['login'])) {
        echo "<p>✅ POST処理開始</p>";
        
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        
        echo "<p>Email sanitized: " . htmlspecialchars($email) . "</p>";
        echo "<p>Password length: " . strlen($password) . "</p>";
        
        $errors = [];
        
        if (empty($email)) $errors[] = 'メールアドレスを入力してください';
        if (empty($password)) $errors[] = 'パスワードを入力してください';
        
        echo "<p>バリデーション完了。エラー数: " . count($errors) . "</p>";
        
        if (empty($errors)) {
            echo "<p>✅ バリデーション通過</p>";
            
            echo "<p>データベース検索開始...</p>";
            $shop_admin = $db->fetch(
                "SELECT sa.*, s.name as shop_name, s.status as shop_status, s.verification_code
                 FROM shop_admins sa
                 JOIN shops s ON sa.shop_id = s.id
                 WHERE sa.email = ? AND sa.status = 'active'",
                [$email]
            );
            
            if ($shop_admin) {
                echo "<p>✅ Shop admin found</p>";
                echo "<ul>";
                echo "<li>ID: " . $shop_admin['id'] . "</li>";
                echo "<li>Email: " . htmlspecialchars($shop_admin['email']) . "</li>";
                echo "<li>Username: " . htmlspecialchars($shop_admin['username']) . "</li>";
                echo "<li>Shop ID: " . $shop_admin['shop_id'] . "</li>";
                echo "<li>Shop Name: " . htmlspecialchars($shop_admin['shop_name']) . "</li>";
                echo "<li>Shop Status: " . htmlspecialchars($shop_admin['shop_status']) . "</li>";
                echo "<li>Verification Code: " . htmlspecialchars($shop_admin['verification_code'] ?? 'NULL') . "</li>";
                echo "</ul>";
                
                echo "<p>パスワード検証...</p>";
                if (verify_password($password, $shop_admin['password_hash'])) {
                    echo "<p style='color: green;'>✅ Password verified</p>";
                    
                    echo "<p>セッション情報設定...</p>";
                    $_SESSION['shop_admin_id'] = $shop_admin['id'];
                    $_SESSION['shop_admin_email'] = $shop_admin['email'];
                    $_SESSION['shop_admin_username'] = $shop_admin['username'];
                    $_SESSION['shop_id'] = $shop_admin['shop_id'];
                    $_SESSION['shop_name'] = $shop_admin['shop_name'];
                    $_SESSION['shop_status'] = $shop_admin['shop_status'];
                    $_SESSION['verification_code'] = $shop_admin['verification_code'];
                    
                    echo "<p style='color: green;'>✅ Session data set</p>";
                    
                    echo "<h3>設定後のセッション内容:</h3>";
                    echo "<pre>";
                    print_r($_SESSION);
                    echo "</pre>";
                    
                    echo "<h3>リダイレクト先決定:</h3>";
                    if ($shop_admin['shop_status'] === 'verification_pending') {
                        echo "<p style='color: orange;'>住所確認ページ: shop_admin/verify_address.php</p>";
                    } else {
                        echo "<p style='color: blue;'>ダッシュボード: ?page=shop_dashboard</p>";
                    }
                    
                } else {
                    echo "<p style='color: red;'>❌ Password verification failed</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Shop admin not found</p>";
                
                // デバッグ用：shop_adminsテーブルの内容を確認
                echo "<h3>shop_adminsテーブルの内容:</h3>";
                $all_admins = $db->fetchAll("SELECT id, email, username, status FROM shop_admins");
                if ($all_admins) {
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Status</th></tr>";
                    foreach ($all_admins as $admin) {
                        echo "<tr>";
                        echo "<td>" . $admin['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($admin['status']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>shop_adminsテーブルにデータがありません</p>";
                }
            }
        } else {
            echo "<h3>バリデーションエラー</h3>";
            foreach ($errors as $error) {
                echo "<p style='color: red;'>❌ " . htmlspecialchars($error) . "</p>";
            }
        }
    } else {
        echo "<p>⚠️ POST処理条件に合致しません</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>デバッグ完了</h2>";
?>
