<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes.php';

// 管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

$page_title = '店舗管理者アカウント作成';

// 店舗管理者アカウント作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_id = (int)$_POST['shop_id'];
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    $errors = [];
    
    // バリデーション
    if (empty($shop_id)) {
        $errors[] = '店舗を選択してください。';
    }
    
    if (empty($username)) {
        $errors[] = 'ユーザー名を入力してください。';
    } elseif (strlen($username) < 3) {
        $errors[] = 'ユーザー名は3文字以上で入力してください。';
    }
    
    if (empty($email)) {
        $errors[] = 'メールアドレスを入力してください。';
    } elseif (!validate_email($email)) {
        $errors[] = '正しいメールアドレスを入力してください。';
    }
    
    if (empty($password)) {
        $errors[] = 'パスワードを入力してください。';
    } elseif (strlen($password) < 6) {
        $errors[] = 'パスワードは6文字以上で入力してください。';
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'パスワードが一致しません。';
    }
    
    // 重複チェック
    if (empty($errors)) {
        $existing_admin = $db->fetch(
            "SELECT id FROM shop_admins WHERE username = ? OR email = ?",
            [$username, $email]
        );
        
        if ($existing_admin) {
            $errors[] = 'このユーザー名またはメールアドレスは既に使用されています。';
        }
    }
    
    if (empty($errors)) {
        try {
            $db->query(
                "INSERT INTO shop_admins (shop_id, username, email, password_hash, status) 
                 VALUES (?, ?, ?, ?, 'active')",
                [
                    $shop_id,
                    $username,
                    $email,
                    hash_password($password)
                ]
            );
            
            $_SESSION['success_message'] = '店舗管理者アカウントが正常に作成されました。';
            header('Location: create_shop_admin.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'アカウント作成中にエラーが発生しました: ' . $e->getMessage();
        }
    }
}

// 店舗一覧取得
$shops = $db->fetchAll("SELECT id, name FROM shops ORDER BY name");

ob_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- 管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>管理者パネル
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">
                    <i class="fas fa-user-plus me-2"></i>店舗管理者アカウント作成
                </h1>
            </div>
        </div>
        
        <!-- 成功・エラーメッセージ -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">店舗管理者アカウント作成</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="shop_id" class="form-label">店舗 *</label>
                                    <select class="form-select" id="shop_id" name="shop_id" required>
                                        <option value="">店舗を選択してください</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?php echo $shop['id']; ?>" 
                                                    <?php echo (isset($_POST['shop_id']) && $_POST['shop_id'] == $shop['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($shop['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        店舗を選択してください。
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">ユーザー名 *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                           required>
                                    <div class="form-text">3文字以上の英数字とアンダースコア</div>
                                    <div class="invalid-feedback">
                                        ユーザー名を入力してください。
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">メールアドレス *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        正しいメールアドレスを入力してください。
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">パスワード *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">6文字以上</div>
                                    <div class="invalid-feedback">
                                        パスワードを入力してください。
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirm" class="form-label">パスワード確認 *</label>
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                    <div class="invalid-feedback">
                                        パスワードを再入力してください。
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>店舗管理者アカウント作成
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">既存の店舗管理者</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $existing_admins = $db->fetchAll(
                            "SELECT sa.*, s.name as shop_name 
                             FROM shop_admins sa 
                             JOIN shops s ON sa.shop_id = s.id 
                             ORDER BY sa.created_at DESC"
                        );
                        ?>
                        
                        <?php if (empty($existing_admins)): ?>
                            <p class="text-muted">店舗管理者アカウントがありません</p>
                        <?php else: ?>
                            <?php foreach ($existing_admins as $admin): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($admin['username']); ?></h6>
                                        <p class="mb-1 small text-muted"><?php echo htmlspecialchars($admin['shop_name']); ?></p>
                                        <small class="text-muted"><?php echo htmlspecialchars($admin['email']); ?></small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php echo $admin['status'] === 'active' ? 'success' : 'danger'; ?>">
                                            <?php echo $admin['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    
    // パスワード確認のリアルタイムバリデーション
    document.getElementById('password_confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const passwordConfirm = this.value;
        
        if (passwordConfirm && password !== passwordConfirm) {
            this.setCustomValidity('パスワードが一致しません。');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
