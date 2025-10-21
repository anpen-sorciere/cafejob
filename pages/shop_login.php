<?php
$page_title = '店舗ログイン';
$page_description = '店舗管理者のログインを行います。';

// ログイン処理
if ($_POST && isset($_POST['login'])) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($email)) $errors[] = 'メールアドレスを入力してください';
    if (empty($password)) $errors[] = 'パスワードを入力してください';
    
    if (empty($errors)) {
        $shop_admin = $db->fetch(
            "SELECT sa.*, s.name as shop_name, s.status as shop_status
             FROM shop_admins sa
             JOIN shops s ON sa.shop_id = s.id
             WHERE sa.email = ? AND sa.status = 'active'",
            [$email]
        );
        
        if ($shop_admin && verify_password($password, $shop_admin['password_hash'])) {
            $_SESSION['shop_admin_id'] = $shop_admin['id'];
            $_SESSION['shop_admin_email'] = $shop_admin['email'];
            $_SESSION['shop_admin_username'] = $shop_admin['username'];
            $_SESSION['shop_id'] = $shop_admin['shop_id'];
            $_SESSION['shop_name'] = $shop_admin['shop_name'];
            $_SESSION['shop_status'] = $shop_admin['shop_status'];
            
            $_SESSION['success_message'] = 'ログインしました。';
            
            // 住所確認が必要な場合は確認ページにリダイレクト
            if ($shop_admin['shop_status'] === 'verification_pending') {
                header('Location: shop_admin/verify_address.php');
                exit;
            }
            
            header('Location: ?page=shop_dashboard');
            exit;
        } else {
            $errors[] = 'メールアドレスまたはパスワードが正しくありません';
        }
    }
}

ob_start();
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-store me-2"></i>店舗ログイン
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
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
                            <button type="submit" name="login" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="text-muted mb-2">店舗登録がお済みでない方は</p>
                        <a href="?page=shop_register" class="btn btn-outline-primary">
                            <i class="fas fa-store me-2"></i>店舗登録
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
