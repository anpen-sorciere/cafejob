<?php
// 店舗ログイン デバッグ版
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

echo "<h1>店舗ログイン デバッグ版</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        echo "セッション開始: 成功<br>";
    } else {
        echo "セッション既に開始済み<br>";
    }
    
    echo "<h2>2. ファイル読み込み</h2>";
    require_once '../config/config.php';
    echo "config.php読み込み: 成功<br>";
    
    require_once '../config/database.php';
    echo "database.php読み込み: 成功<br>";
    
    require_once '../includes/functions.php';
    echo "functions.php読み込み: 成功<br>";
    
    echo "<h2>3. データベース接続</h2>";
    $db = new Database();
    echo "データベース接続: 成功<br>";
    
    echo "<h2>4. POSTデータ確認</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    if ($_POST && isset($_POST['login'])) {
        echo "<h2>5. ログイン処理開始</h2>";
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        echo "メールアドレス: " . htmlspecialchars($email) . "<br>";
        echo "パスワード: " . (empty($password) ? "空" : "入力済み") . "<br>";
        
        if (function_exists('sanitize_input')) {
            $email = sanitize_input($email);
            echo "sanitize_input実行: 成功<br>";
        } else {
            echo "sanitize_input関数が存在しません<br>";
        }
        
        $errors = [];
        
        if (empty($email)) $errors[] = 'メールアドレスを入力してください';
        if (empty($password)) $errors[] = 'パスワードを入力してください';
        
        echo "バリデーション結果: " . (empty($errors) ? "OK" : implode(", ", $errors)) . "<br>";
        
        if (empty($errors)) {
            echo "<h2>6. データベースクエリ実行</h2>";
            
            $query = "SELECT sa.*, s.name as shop_name, s.status as shop_status, s.verification_code
                     FROM shop_admins sa
                     JOIN shops s ON sa.shop_id = s.id
                     WHERE sa.email = ? AND sa.status = 'active'";
            
            echo "クエリ: " . htmlspecialchars($query) . "<br>";
            echo "パラメータ: " . htmlspecialchars($email) . "<br>";
            
            $shop_admin = $db->fetch($query, [$email]);
            
            if ($shop_admin) {
                echo "店舗管理者データ取得: 成功<br>";
                echo "<pre>";
                print_r($shop_admin);
                echo "</pre>";
                
                if (function_exists('verify_password')) {
                    $password_valid = verify_password($password, $shop_admin['password_hash']);
                    echo "パスワード検証: " . ($password_valid ? "成功" : "失敗") . "<br>";
                    
                    if ($password_valid) {
                        echo "<h2>7. セッション設定</h2>";
                        $_SESSION['shop_admin_id'] = $shop_admin['id'];
                        $_SESSION['shop_admin_email'] = $shop_admin['email'];
                        $_SESSION['shop_admin_username'] = $shop_admin['username'];
                        $_SESSION['shop_id'] = $shop_admin['shop_id'];
                        $_SESSION['shop_name'] = $shop_admin['shop_name'];
                        $_SESSION['shop_status'] = $shop_admin['shop_status'];
                        $_SESSION['verification_code'] = $shop_admin['verification_code'];
                        
                        echo "セッション設定: 成功<br>";
                        echo "<pre>";
                        print_r($_SESSION);
                        echo "</pre>";
                        
                        echo "<h2>8. リダイレクト準備</h2>";
                        if ($shop_admin['shop_status'] === 'verification_pending') {
                            echo "住所確認が必要: verify_address.phpにリダイレクト<br>";
                            echo '<a href="../shop_admin/verify_address.php">住所確認ページへ</a><br>';
                        } else {
                            echo "通常ログイン: dashboard.phpにリダイレクト<br>";
                            echo '<a href="../shop_admin/dashboard.php">ダッシュボードへ</a><br>';
                        }
                    } else {
                        echo "パスワードが正しくありません<br>";
                    }
                } else {
                    echo "verify_password関数が存在しません<br>";
                }
            } else {
                echo "店舗管理者データが見つかりません<br>";
            }
        }
    } else {
        echo "<h2>5. ログインフォーム表示</h2>";
        ?>
        <form method="POST">
            <div>
                <label>メールアドレス:</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>パスワード:</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <button type="submit" name="login">ログイン</button>
            </div>
        </form>
        <?php
    }
    
} catch (Exception $e) {
    echo "<h2>エラー発生</h2>";
    echo "エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "エラーファイル: " . $e->getFile() . "<br>";
    echo "エラー行: " . $e->getLine() . "<br>";
    echo "<pre>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<h2>現在のセッションデータ</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
