<?php
// 最小限の店舗ログインテスト
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>店舗ログインテスト</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    if (session_status() === PHP_SESSION_NONE) {
        session_name('cafejob_session');
        session_start();
        echo "<p style='color: green;'>セッション開始成功</p>";
    } else {
        echo "<p style='color: orange;'>セッション既に開始済み</p>";
    }
    
    echo "<h2>2. ファイル読み込み</h2>";
    require_once 'config/config.php';
    echo "<p style='color: green;'>config.php読み込み成功</p>";
    
    require_once 'config/database.php';
    echo "<p style='color: green;'>database.php読み込み成功</p>";
    
    require_once 'includes/functions.php';
    echo "<p style='color: green;'>functions.php読み込み成功</p>";
    
    echo "<h2>3. データベース接続</h2>";
    $db = new Database();
    echo "<p style='color: green;'>データベース接続成功</p>";
    
    echo "<h2>4. ログイン処理テスト</h2>";
    if ($_POST && isset($_POST['test_login'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        echo "<p><strong>入力されたメール:</strong> " . htmlspecialchars($email) . "</p>";
        echo "<p><strong>パスワード長:</strong> " . strlen($password) . "</p>";
        
        if (!empty($email) && !empty($password)) {
            try {
                $shop_admin = $db->fetch(
                    "SELECT sa.*, s.name as shop_name, s.status as shop_status, s.verification_code
                     FROM shop_admins sa
                     JOIN shops s ON sa.shop_id = s.id
                     WHERE sa.email = ? AND sa.status = 'active'",
                    [$email]
                );
                
                if ($shop_admin) {
                    echo "<p style='color: green;'>店舗管理者データ取得成功</p>";
                    echo "<p><strong>ID:</strong> " . $shop_admin['id'] . "</p>";
                    echo "<p><strong>店舗名:</strong> " . htmlspecialchars($shop_admin['shop_name']) . "</p>";
                    echo "<p><strong>ステータス:</strong> " . $shop_admin['shop_status'] . "</p>";
                    
                    if (verify_password($password, $shop_admin['password_hash'])) {
                        echo "<p style='color: green;'>パスワード検証成功</p>";
                        
                        // セッション設定
                        $_SESSION['shop_admin_id'] = $shop_admin['id'];
                        $_SESSION['shop_admin_email'] = $shop_admin['email'];
                        $_SESSION['shop_admin_username'] = $shop_admin['username'];
                        $_SESSION['shop_id'] = $shop_admin['shop_id'];
                        $_SESSION['shop_name'] = $shop_admin['shop_name'];
                        $_SESSION['shop_status'] = $shop_admin['shop_status'];
                        $_SESSION['verification_code'] = $shop_admin['verification_code'];
                        
                        echo "<p style='color: green;'>セッション設定完了</p>";
                        echo "<p><a href='shop_admin/dashboard.php' class='btn btn-primary'>ダッシュボードへ</a></p>";
                    } else {
                        echo "<p style='color: red;'>パスワード検証失敗</p>";
                    }
                } else {
                    echo "<p style='color: red;'>店舗管理者データが見つかりません</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>データベースエラー: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>メールアドレスまたはパスワードが空です</p>";
        }
    }
    
    echo "<h2>5. ログインフォーム</h2>";
    ?>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <button type="submit" name="test_login" class="btn btn-primary">テストログイン</button>
    </form>
    
    <h2>6. セッション情報</h2>
    <pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>
<?php print_r($_SESSION); ?>
    </pre>
    
    <?php
    
} catch (Exception $e) {
    echo "<h2>エラー発生</h2>";
    echo "<p style='color: red;'>エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: red;'>エラーファイル: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p style='color: red;'>エラー行: " . $e->getLine() . "</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

?>
