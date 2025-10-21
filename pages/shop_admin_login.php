<?php
$page_title = '店舗管理者ログイン';
$page_description = 'カフェJob店舗管理者パネルにログインしてください。';

// 店舗管理者ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error_message = 'メールアドレスとパスワードを入力してください。';
    } else {
        $shop_admin = $db->fetch(
            "SELECT sa.*, s.name as shop_name, s.id as shop_id
             FROM shop_admins sa
             JOIN shops s ON sa.shop_id = s.id
             WHERE sa.email = ? AND sa.status = 'active'",
            [$email]
        );
        
        if ($shop_admin && verify_password($password, $shop_admin['password_hash'])) {
            $_SESSION['shop_admin_id'] = $shop_admin['id'];
            $_SESSION['shop_admin_email'] = $shop_admin['email'];
            $_SESSION['shop_id'] = $shop_admin['shop_id'];
            $_SESSION['shop_name'] = $shop_admin['shop_name'];
            
            header('Location: shop_admin/dashboard.php');
            exit;
        } else {
            $error_message = 'メールアドレスまたはパスワードが正しくありません。';
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
                            <i class="fas fa-store me-2 text-primary"></i>店舗管理者ログイン
                        </h2>
                        <p class="text-muted">店舗管理パネルにアクセスしてください</p>
                    </div>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>メールアドレス
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   required>
                            <div class="invalid-feedback">
                                メールアドレスを入力してください。
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
                        
                        <div class="mb-3">
                            <a href="?page=shop_forgot_password" class="text-decoration-none">
                                パスワードを忘れた方はこちら
                            </a>
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
            
            <!-- 店舗登録案内 -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2 text-info"></i>店舗登録について
                    </h6>
                    <p class="card-text small text-muted mb-2">
                        まだ店舗登録をされていない場合は、まず店舗登録を行ってください。
                    </p>
                    <div class="d-grid">
                        <a href="?page=shop_register" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-store me-1"></i>店舗登録
                        </a>
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
