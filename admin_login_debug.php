<?php
// シンプルな管理者ログインページ（デバッグ用）
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "管理者ログインページ（デバッグ版）<br>";

try {
    echo "1. セッション開始...<br>";
    session_start();
    
    echo "2. 設定ファイル読み込み...<br>";
    require_once 'config/config.php';
    
    echo "3. 関数ファイル読み込み...<br>";
    require_once 'includes/functions.php';
    
    echo "4. データベース接続...<br>";
    require_once 'config/database.php';
    
    echo "5. 管理者テーブル確認...<br>";
    $result = $db->fetch("SELECT COUNT(*) as count FROM admins");
    echo "adminsテーブル: " . $result['count'] . "件<br>";
    
    echo "6. 管理者ログイン処理...<br>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "POST処理開始<br>";
        $username = sanitize_input($_POST['username']);
        $password = $_POST['password'];
        
        echo "ユーザー名: " . $username . "<br>";
        
        $admin = $db->fetch(
            "SELECT * FROM admins WHERE username = ? AND status = 'active'",
            [$username]
        );
        
        if ($admin && verify_password($password, $admin['password_hash'])) {
            echo "ログイン成功<br>";
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            echo "セッション設定完了<br>";
            echo "リダイレクト先: admin/index.php<br>";
            // header('Location: admin/index.php');
            // exit;
        } else {
            echo "ログイン失敗<br>";
        }
    }
    
    echo "7. フォーム表示...<br>";
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "<br>";
    echo "ファイル: " . $e->getFile() . "<br>";
    echo "行: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "Fatal Error: " . $e->getMessage() . "<br>";
    echo "ファイル: " . $e->getFile() . "<br>";
    echo "行: " . $e->getLine() . "<br>";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン（デバッグ版）</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">管理者ログイン（デバッグ版）</h2>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">ユーザー名</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">パスワード</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">ログイン</button>
                            </div>
                        </form>
                        
                        <div class="mt-3">
                            <h6>デモ管理者アカウント</h6>
                            <p>ユーザー名: admin</p>
                            <p>パスワード: admin123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
