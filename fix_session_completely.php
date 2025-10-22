<?php
// セッション競合完全解決ファイル
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>セッション競合解決</h1>";

// 1. 既存のセッションを完全に破棄
echo "<h2>1. 既存セッション破棄</h2>";

// すべてのセッション名を試して破棄
$session_names = ['PHPSESSID', 'cafejob_session', 'netpgpos_session', 'default_session'];
foreach ($session_names as $session_name) {
    session_name($session_name);
    session_start();
    session_destroy();
    echo "<p>セッション '{$session_name}' を破棄しました</p>";
}

// セッションクッキーも削除
if (isset($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'PHPSESSID') !== false || 
            strpos($name, 'cafejob') !== false || 
            strpos($name, 'netpgpos') !== false) {
            setcookie($name, '', time() - 3600, '/');
            echo "<p>クッキー '{$name}' を削除しました</p>";
        }
    }
}

// 2. 新しいcafejob専用セッションを開始
echo "<h2>2. 新しいcafejobセッション開始</h2>";

// セッション設定をリセット
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTPSでない場合は0
ini_set('session.cookie_samesite', 'Lax');

// cafejob専用セッション名を設定
session_name('cafejob_session');
session_start();

echo "<p style='color: green;'>cafejob専用セッション開始成功</p>";
echo "<p><strong>セッション名:</strong> " . session_name() . "</p>";
echo "<p><strong>セッションID:</strong> " . session_id() . "</p>";

// 3. セッションデータをクリア
$_SESSION = array();
echo "<p>セッションデータをクリアしました</p>";

// 4. テストデータを設定
echo "<h2>3. テストデータ設定</h2>";
$_SESSION['test_data'] = 'cafejob_test_' . time();
$_SESSION['session_created'] = date('Y-m-d H:i:s');
echo "<p style='color: green;'>テストデータを設定しました</p>";

// 5. セッション情報表示
echo "<h2>4. セッション情報確認</h2>";
echo "<h3>現在のセッション</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>セッション設定</h3>";
echo "<p><strong>session.name:</strong> " . ini_get('session.name') . "</p>";
echo "<p><strong>session.cookie_lifetime:</strong> " . ini_get('session.cookie_lifetime') . "</p>";
echo "<p><strong>session.cookie_path:</strong> " . ini_get('session.cookie_path') . "</p>";
echo "<p><strong>session.cookie_domain:</strong> " . ini_get('session.cookie_domain') . "</p>";

// 6. クッキー情報表示
echo "<h2>5. クッキー情報確認</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
print_r($_COOKIE);
echo "</pre>";

// 7. ファイル読み込みテスト
echo "<h2>6. ファイル読み込みテスト</h2>";
try {
    require_once 'config/config.php';
    echo "<p style='color: green;'>config.php読み込み成功</p>";
    
    require_once 'config/database.php';
    echo "<p style='color: green;'>database.php読み込み成功</p>";
    
    require_once 'includes/functions.php';
    echo "<p style='color: green;'>functions.php読み込み成功</p>";
    
    // データベース接続テスト
    $db = new Database();
    echo "<p style='color: green;'>データベース接続成功</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 8. 店舗管理者認証テスト
echo "<h2>7. 店舗管理者認証テスト</h2>";
if (function_exists('is_shop_admin')) {
    $is_admin = is_shop_admin();
    echo "<p><strong>is_shop_admin()結果:</strong> " . ($is_admin ? 'true' : 'false') . "</p>";
} else {
    echo "<p style='color: red;'>is_shop_admin()関数が存在しません</p>";
}

// 9. ログインテストフォーム
echo "<h2>8. ログインテスト</h2>";
if ($_POST && isset($_POST['test_login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h3>ログイン処理テスト</h3>";
    echo "<p><strong>入力メール:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>パスワード長:</strong> " . strlen($password) . "</p>";
    
    if (!empty($email) && !empty($password)) {
        try {
            $shop_admin = $db->fetch(
                "SELECT sa.*, s.name as shop_name, s.status as shop_status
                 FROM shop_admins sa
                 JOIN shops s ON sa.shop_id = s.id
                 WHERE sa.email = ? AND sa.status = 'active'",
                [$email]
            );
            
            if ($shop_admin) {
                echo "<p style='color: green;'>店舗管理者データ取得成功</p>";
                echo "<p><strong>ID:</strong> " . $shop_admin['id'] . "</p>";
                echo "<p><strong>店舗名:</strong> " . htmlspecialchars($shop_admin['shop_name']) . "</p>";
                
                if (verify_password($password, $shop_admin['password_hash'])) {
                    echo "<p style='color: green;'>パスワード検証成功</p>";
                    
                    // セッション設定
                    $_SESSION['shop_admin_id'] = $shop_admin['id'];
                    $_SESSION['shop_admin_email'] = $shop_admin['email'];
                    $_SESSION['shop_admin_username'] = $shop_admin['username'];
                    $_SESSION['shop_id'] = $shop_admin['shop_id'];
                    $_SESSION['shop_name'] = $shop_admin['shop_name'];
                    $_SESSION['shop_status'] = $shop_admin['shop_status'];
                    
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
    }
}

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

<h2>9. 最終セッション確認</h2>
<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>
<?php print_r($_SESSION); ?>
</pre>

<p><strong>注意:</strong> このページを実行後、ブラウザを完全に閉じて再度開いてから店舗ログインを試してください。</p>
