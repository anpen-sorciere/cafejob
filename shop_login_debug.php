<?php
// 店舗ログイン処理の詳細ログ版
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// ログファイルのパス
$log_file = __DIR__ . '/logs/shop_login_debug.log';

// ログ出力関数
function write_log($message, $data = null) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";
    if ($data !== null) {
        $log_message .= " | Data: " . print_r($data, true);
    }
    $log_message .= "\n";
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

// ログファイルをクリア
file_put_contents($log_file, '');

write_log("=== 店舗ログイン処理デバッグ開始 ===");

try {
    write_log("1. セッション処理");
    if (session_status() === PHP_SESSION_NONE) {
        session_name('cafejob_session');
        session_start();
        write_log("セッション開始", ['session_name' => session_name(), 'session_id' => session_id()]);
    } else {
        write_log("セッション既に開始済み", ['session_name' => session_name(), 'session_id' => session_id()]);
    }
    
    write_log("2. ファイル読み込み");
    require_once 'config/config.php';
    write_log("config.php読み込み成功");
    
    require_once 'config/database.php';
    write_log("database.php読み込み成功");
    
    require_once 'includes/functions.php';
    write_log("functions.php読み込み成功");
    
    write_log("3. データベース接続");
    $db = new Database();
    write_log("データベース接続成功");
    
    write_log("4. POSTデータ確認");
    write_log("POSTデータ", $_POST);
    
    if ($_POST && isset($_POST['login'])) {
        write_log("5. ログイン処理開始");
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        write_log("入力データ", ['email' => $email, 'password_length' => strlen($password)]);
        
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'メールアドレスを入力してください';
            write_log("バリデーションエラー: メールアドレス空");
        }
        if (empty($password)) {
            $errors[] = 'パスワードを入力してください';
            write_log("バリデーションエラー: パスワード空");
        }
        
        write_log("バリデーション結果", ['errors' => $errors]);
        
        if (empty($errors)) {
            write_log("6. データベースクエリ実行");
            
            $query = "SELECT sa.*, s.name as shop_name, s.status as shop_status, s.verification_code
                     FROM shop_admins sa
                     JOIN shops s ON sa.shop_id = s.id
                     WHERE sa.email = ? AND sa.status = 'active'";
            
            write_log("クエリ実行", ['query' => $query, 'email' => $email]);
            
            $shop_admin = $db->fetch($query, [$email]);
            
            if ($shop_admin) {
                write_log("店舗管理者データ取得成功", $shop_admin);
                
                if (function_exists('verify_password')) {
                    $password_valid = verify_password($password, $shop_admin['password_hash']);
                    write_log("パスワード検証結果", $password_valid);
                    
                    if ($password_valid) {
                        write_log("7. セッション設定開始");
                        
                        $_SESSION['shop_admin_id'] = $shop_admin['id'];
                        $_SESSION['shop_admin_email'] = $shop_admin['email'];
                        $_SESSION['shop_admin_username'] = $shop_admin['username'];
                        $_SESSION['shop_id'] = $shop_admin['shop_id'];
                        $_SESSION['shop_name'] = $shop_admin['shop_name'];
                        $_SESSION['shop_status'] = $shop_admin['shop_status'];
                        $_SESSION['verification_code'] = $shop_admin['verification_code'];
                        
                        write_log("セッション設定完了", $_SESSION);
                        
                        write_log("8. リダイレクト処理");
                        if ($shop_admin['shop_status'] === 'verification_pending') {
                            $_SESSION['address_verification_pending'] = true;
                            write_log("住所確認が必要: verify_address.phpにリダイレクト");
                            header('Location: ../shop_admin/verify_address.php');
                            exit;
                        } else {
                            write_log("通常ログイン: dashboard.phpにリダイレクト");
                            header('Location: ../shop_admin/dashboard.php');
                            exit;
                        }
                    } else {
                        write_log("パスワード検証失敗");
                        $errors[] = 'メールアドレスまたはパスワードが正しくありません';
                    }
                } else {
                    write_log("verify_password関数が存在しません");
                    $errors[] = 'システムエラーが発生しました';
                }
            } else {
                write_log("店舗管理者データが見つかりません");
                $errors[] = 'メールアドレスまたはパスワードが正しくありません';
            }
        }
    } else {
        write_log("ログインフォーム表示");
    }
    
    write_log("=== 店舗ログイン処理デバッグ完了 ===");
    
} catch (Exception $e) {
    write_log("=== エラー発生 ===");
    write_log("エラーメッセージ", $e->getMessage());
    write_log("エラーファイル", $e->getFile());
    write_log("エラー行", $e->getLine());
    write_log("スタックトレース", $e->getTraceAsString());
}

// HTML表示
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗ログイン デバッグ版</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1>店舗ログイン デバッグ版</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5>エラー</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>ログインフォーム</h5>
                    </div>
                    <div class="card-body">
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
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="login" class="btn btn-primary">
                                    ログイン
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>デバッグ情報</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>ログファイル:</strong> <code><?php echo $log_file; ?></code></p>
                        
                        <?php if (file_exists($log_file)): ?>
                            <h6>ログ内容:</h6>
                            <pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto; font-size: 12px;"><?php echo htmlspecialchars(file_get_contents($log_file)); ?></pre>
                        <?php else: ?>
                            <p>ログファイルが存在しません。</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
