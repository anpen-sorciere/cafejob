<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>店舗ログイン処理 デバッグ</h1>";

try {
    require_once 'config/config.php';
    require_once 'includes/functions.php';
    
    echo "<h2>1. 基本設定確認</h2>";
    echo "<p>✅ Files loaded</p>";
    
    session_start();
    echo "<p>✅ Session started</p>";
    
    echo "<h2>2. テスト用ログイン処理</h2>";
    
    // テスト用のPOSTデータをシミュレート
    $_POST = [
        'login' => '1',
        'email' => 'admin@example.com', // 実際に登録したメールアドレスに変更
        'password' => 'password123'      // 実際に登録したパスワードに変更
    ];
    
    echo "<p>テスト用POSTデータ:</p>";
    echo "<ul>";
    echo "<li>Email: " . htmlspecialchars($_POST['email']) . "</li>";
    echo "<li>Password: " . (empty($_POST['password']) ? '空' : '入力済み') . "</li>";
    echo "</ul>";
    
    if ($_POST && isset($_POST['login'])) {
        echo "<p>POST処理開始...</p>";
        
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        
        echo "<p>✅ Data sanitized</p>";
        
        $errors = [];
        
        if (empty($email)) $errors[] = 'メールアドレスを入力してください';
        if (empty($password)) $errors[] = 'パスワードを入力してください';
        
        echo "<p>バリデーション完了。エラー数: " . count($errors) . "</p>";
        
        if (empty($errors)) {
            echo "<p>データベース検索開始...</p>";
            
            $shop_admin = $db->fetch(
                "SELECT sa.*, s.name as shop_name, s.id as shop_id, s.status as shop_status, s.verification_code
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
                echo "<li>Shop Status: " . $shop_admin['shop_status'] . "</li>";
                echo "<li>Verification Code: " . htmlspecialchars($shop_admin['verification_code']) . "</li>";
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
                    
                    echo "<h3>セッション内容:</h3>";
                    echo "<pre>";
                    print_r($_SESSION);
                    echo "</pre>";
                    
                    echo "<h3>リダイレクト先:</h3>";
                    if ($shop_admin['shop_status'] === 'verification_pending') {
                        echo "<p>住所確認ページ: shop_admin/verify_address.php</p>";
                    } else {
                        echo "<p>ダッシュボード: ?page=shop_dashboard</p>";
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
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>デバッグ完了</h2>";
?>
