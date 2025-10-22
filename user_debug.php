<?php
// デモユーザーログイン デバッグ版
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/config_simple.php';
require_once 'config/database.php';

echo "<h1>デモユーザーログイン デバッグ</h1>";

// データベース接続確認
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ データベース接続成功<br><br>";
} catch (PDOException $e) {
    echo "❌ データベース接続エラー: " . $e->getMessage() . "<br><br>";
    exit;
}

// ユーザーテーブルの内容確認
echo "<h2>ユーザーテーブルの内容</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "❌ ユーザーレコードが見つかりません<br>";
    } else {
        echo "✅ ユーザーレコード数: " . count($users) . "<br>";
        foreach ($users as $user) {
            echo "ID: " . $user['id'] . "<br>";
            echo "Username: " . $user['username'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
            echo "Password Hash: " . $user['password_hash'] . "<br>";
            echo "Status: " . $user['status'] . "<br>";
            echo "---<br>";
        }
    }
} catch (PDOException $e) {
    echo "❌ クエリエラー: " . $e->getMessage() . "<br>";
}

// パスワード検証テスト
echo "<h2>パスワード検証テスト</h2>";
$test_password = 'demo123';
$test_hash = '$2y$10$W1F6H9KSs1QKWjiDiLlOBuzSUHWj8KM.CVWLbWQIhnEl81c.kC1uK';

echo "テストパスワード: $test_password<br>";
echo "テストハッシュ: $test_hash<br>";
echo "password_verify結果: " . (password_verify($test_password, $test_hash) ? '✅ 成功' : '❌ 失敗') . "<br><br>";

// ログイン処理のテスト
echo "<h2>ログイン処理テスト</h2>";
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "入力されたユーザー名: $username<br>";
    echo "入力されたパスワード: $password<br>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✅ ユーザーが見つかりました<br>";
            echo "データベースのハッシュ: " . $user['password_hash'] . "<br>";
            echo "password_verify結果: " . (password_verify($password, $user['password_hash']) ? '✅ 成功' : '❌ 失敗') . "<br>";
            
            if (password_verify($password, $user['password_hash'])) {
                echo "✅ ログイン成功！<br>";
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
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
        <input type="text" name="username" value="demo_user" required>
    </p>
    <p>
        <label>パスワード:</label><br>
        <input type="password" name="password" value="demo123" required>
    </p>
    <p>
        <input type="submit" value="ログイン">
    </p>
</form>

<?php
// セッション情報の表示
echo "<h2>セッション情報</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "✅ ログイン済み<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Username: " . $_SESSION['username'] . "<br>";
} else {
    echo "❌ ログインしていません<br>";
}
?>




