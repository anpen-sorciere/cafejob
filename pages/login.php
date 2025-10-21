<?php
$page_title = 'ログイン';
$page_description = 'カフェJobにログインして、求人応募やお気に入り機能をご利用ください。';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = 'ユーザー名とパスワードを入力してください。';
    } else {
        $user = $db->fetch(
            "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'",
            [$username, $username]
        );
        
        if ($user && verify_password($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['last_name'] . ' ' . $user['first_name'];
            
            $redirect_url = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header('Location: ' . $redirect_url);
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
                            <i class="fas fa-sign-in-alt me-2 text-primary"></i>ログイン
                        </h2>
                        <p class="text-muted">アカウントにログインしてください</p>
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
                                <i class="fas fa-user me-1"></i>ユーザー名またはメールアドレス
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                   required>
                            <div class="invalid-feedback">
                                ユーザー名またはメールアドレスを入力してください。
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
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                ログイン状態を保持する
                            </label>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-2">
                            <a href="?page=forgot_password" class="text-decoration-none">
                                <i class="fas fa-key me-1"></i>パスワードを忘れた方
                            </a>
                        </p>
                        <p class="mb-0">
                            アカウントをお持ちでない方は
                            <a href="?page=register" class="text-decoration-none fw-bold">
                                新規登録
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- デモアカウント情報 -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2 text-info"></i>デモアカウント
                    </h6>
                    <p class="card-text small text-muted mb-2">
                        テスト用のアカウントでログインできます
                    </p>
                    <div class="row">
                        <div class="col-6">
                            <strong>ユーザー名:</strong><br>
                            <code>demo_user</code>
                        </div>
                        <div class="col-6">
                            <strong>パスワード:</strong><br>
                            <code>demo123</code>
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



