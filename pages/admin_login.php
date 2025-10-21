<?php
// 一時的にアクセス制限を無効化（デバッグ用）
// $allowed_hosts = ['localhost', '127.0.0.1', 'purplelion51.sakura.ne.jp'];
// $current_host = $_SERVER['HTTP_HOST'] ?? '';
// $allowed_ips = ['127.0.0.1', '::1'];
// $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
// if (!in_array($current_host, $allowed_hosts) && !in_array($client_ip, $allowed_ips)) {
//     http_response_code(403);
//     die('アクセスが拒否されました。');
// }

$page_title = '管理者ログイン';
$page_description = 'カフェJob管理者パネルにログインしてください。';

// 管理者ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = 'ユーザー名とパスワードを入力してください。';
    } else {
        $admin = $db->fetch(
            "SELECT * FROM admins WHERE username = ? AND status = 'active'",
            [$username]
        );
        
        if ($admin && verify_password($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            header('Location: admin/index.php');
            exit;
        } else {
            $error_message = 'ユーザー名またはパスワードが正しくありません。';
        }
    }
}

ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">
                            <i class="fas fa-shield-alt me-2 text-primary"></i>管理者ログイン
                        </h2>
                        <p class="text-muted">管理者パネルにアクセスしてください</p>
                    </div>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i>ユーザー名
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                   required>
                            <div class="invalid-feedback">
                                ユーザー名を入力してください。
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>パスワード
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                パスワードを入力してください。
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>サイトトップに戻る
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- デモ管理者アカウント情報 -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2 text-info"></i>デモ管理者アカウント
                    </h6>
                    <p class="card-text small text-muted mb-2">
                        テスト用の管理者アカウントでログインできます
                    </p>
                    <div class="row">
                        <div class="col-6">
                            <strong>ユーザー名:</strong><br>
                            <code>admin</code>
                        </div>
                        <div class="col-6">
                            <strong>パスワード:</strong><br>
                            <code>admin123</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>



