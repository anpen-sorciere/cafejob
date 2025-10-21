<?php
// 管理者ログイン デバッグ版
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/config_simple.php';
require_once 'config/database.php';

echo "<h1>管理者ログイン デバッグ</h1>";

// データベース接続確認
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ データベース接続成功<br><br>";
} catch (PDOException $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br><br>";
    exit;
}

// 管理者テーブルの内容確認
echo "<h2>管理者テーブルの内容</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "❌ 管理者レコードが見つかりません<br>";
    } else {
        echo "✅ 管理者レコード数: " . count($admins) . "<br>";
        foreach ($admins as $admin) {
            echo "ID: " . $admin['id'] . "<br>";
            echo "Username: " . $admin['username'] . "<br>";
            echo "Email: " . $admin['email'] . "<br>";
            echo "Password Hash: " . $admin['password_hash'] . "<br>";
            echo "Role: " . $admin['role'] . "<br>";
            echo "Status: " . $admin['status'] . "<br>";
            echo "---<br>";
        }
    }
} catch (PDOException $e) {
    echo "❌ クエリエラー: " . $e->getMessage() . "<br>";
}

// パスワード検証テスト
echo "<h2>パスワード検証テスト</h2>";
$test_password = 'admin123';
$test_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "テストパスワード: $test_password<br>";
echo "テストハッシュ: $test_hash<br>";
echo "password_verify結果: " . (password_verify($test_password, $test_hash) ? '✅ 成功' : '❌ 失敗') . "<br><br>";

// 新しいハッシュの生成テスト
echo "<h2>新しいハッシュ生成テスト</h2>";
$new_hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "新しいハッシュ: $new_hash<br>";
echo "新しいハッシュでの検証: " . (password_verify($test_password, $new_hash) ? '✅ 成功' : '❌ 失敗') . "<br><br>";

// ログイン処理のテスト
echo "<h2>ログイン処理テスト</h2>";
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "入力されたユーザー名: $username<br>";
    echo "入力されたパスワード: $password<br>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "✅ ユーザーが見つかりました<br>";
            echo "データベースのハッシュ: " . $admin['password_hash'] . "<br>";
            echo "password_verify結果: " . (password_verify($password, $admin['password_hash']) ? '✅ 成功' : '❌ 失敗') . "<br>";
            
            if (password_verify($password, $admin['password_hash'])) {
                echo "✅ ログイン成功！<br>";
                session_start();
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                echo "セッション情報を設定しました<br>";
            } else {
                echo "❌ パスワードが間違っています<br>";
            }
        } else {
            echo "❌ ユーザーが見つかりません<br>";
        }
    } catch (PDOException $e) {
        echo "❌ クエリエラー: " . $e->getMessage() . "<br>";
    }
}

// ログインフォーム
echo "<h2>ログインフォーム</h2>";
?>
<form method="POST">
    <p>
        <label>ユーザー名:</label><br>
        <input type="text" name="username" value="admin" required>
    </p>
    <p>
        <label>パスワード:</label><br>
        <input type="password" name="password" value="admin123" required>
    </p>
    <p>
        <input type="submit" value="ログイン">
    </p>
</form>

<?php
// セッション情報の表示
echo "<h2>セッション情報</h2>";
session_start();
if (isset($_SESSION['admin_id'])) {
    echo "✅ ログイン済み<br>";
    echo "Admin ID: " . $_SESSION['admin_id'] . "<br>";
    echo "Username: " . $_SESSION['admin_username'] . "<br>";
    echo "Role: " . $_SESSION['admin_role'] . "<br>";
} else {
    echo "❌ ログインしていません<br>";
}
?>

