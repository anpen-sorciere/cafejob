<?php
$page_title = '新規登録';
$page_description = 'カフェJobに新規登録して、求人応募やお気に入り機能をご利用ください。';

// 登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $phone = sanitize_input($_POST['phone']);
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $postal_code = sanitize_input($_POST['postal_code']);
    $prefecture_id = (int)($_POST['prefecture_id'] ?? 0);
    $city_id = (int)($_POST['city_id'] ?? 0);
    $address = sanitize_input($_POST['address']);
    $agree_terms = isset($_POST['agree_terms']);
    
    $errors = [];
    
    // バリデーション
    if (empty($username)) {
        $errors[] = 'ユーザー名を入力してください。';
    } elseif (strlen($username) < 3) {
        $errors[] = 'ユーザー名は3文字以上で入力してください。';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'ユーザー名は英数字とアンダースコアのみ使用できます。';
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
    
    if (empty($first_name)) {
        $errors[] = '名前を入力してください。';
    }
    
    if (empty($last_name)) {
        $errors[] = '姓を入力してください。';
    }
    
    if (!empty($phone) && !preg_match('/^[0-9-+()]+$/', $phone)) {
        $errors[] = '正しい電話番号を入力してください。';
    }
    
    if (!empty($birth_date) && strtotime($birth_date) > strtotime('-13 years')) {
        $errors[] = '13歳以上である必要があります。';
    }
    
    if (!$agree_terms) {
        $errors[] = '利用規約に同意してください。';
    }
    
    // 重複チェック
    if (empty($errors)) {
        $existing_user = $db->fetch(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        );
        
        if ($existing_user) {
            $errors[] = 'このユーザー名またはメールアドレスは既に使用されています。';
        }
    }
    
    if (empty($errors)) {
        try {
            $db->query(
                "INSERT INTO users (username, email, password_hash, first_name, last_name, phone, birth_date, gender, postal_code, prefecture_id, city_id, address, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')",
                [
                    $username,
                    $email,
                    hash_password($password),
                    $first_name,
                    $last_name,
                    $phone ?: null,
                    $birth_date ?: null,
                    $gender ?: null,
                    $postal_code ?: null,
                    $prefecture_id ?: null,
                    $city_id ?: null,
                    $address ?: null
                ]
            );
            
            $_SESSION['success_message'] = 'アカウントが正常に作成されました。ログインしてください。';
            header('Location: ?page=login');
            exit;
        } catch (Exception $e) {
            $errors[] = '登録中にエラーが発生しました。もう一度お試しください。';
        }
    }
}

ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">
                            <i class="fas fa-user-plus me-2 text-primary"></i>新規登録
                        </h2>
                        <p class="text-muted">無料でアカウントを作成してください</p>
                    </div>
                    
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
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>ユーザー名 *
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       required>
                                <div class="form-text">3文字以上の英数字とアンダースコア</div>
                                <div class="invalid-feedback">
                                    ユーザー名を入力してください。
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>メールアドレス *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    正しいメールアドレスを入力してください。
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>パスワード *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">6文字以上</div>
                                <div class="invalid-feedback">
                                    パスワードを入力してください。
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirm" class="form-label">
                                    <i class="fas fa-lock me-1"></i>パスワード確認 *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirm')">
                                        <i class="fas fa-eye" id="passwordConfirmToggleIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    パスワードを再入力してください。
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>姓 *
                                </label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    姓を入力してください。
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>名前 *
                                </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    名前を入力してください。
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>電話番号
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                                       placeholder="090-1234-5678">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">
                                    <i class="fas fa-birthday-cake me-1"></i>生年月日
                                </label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                       value="<?php echo isset($_POST['birth_date']) ? htmlspecialchars($_POST['birth_date']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gender" class="form-label">
                                <i class="fas fa-venus-mars me-1"></i>性別
                            </label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">選択してください</option>
                                <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>男性</option>
                                <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>女性</option>
                                <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>その他</option>
                            </select>
                        </div>
                        
                        <!-- 住所情報 -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">
                                    <i class="fas fa-mail-bulk me-1"></i>郵便番号
                                </label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       placeholder="123-4567" pattern="[0-9]{3}-[0-9]{4}"
                                       value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>">
                                <div class="form-text">例: 123-4567</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="prefecture_id" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>都道府県
                                </label>
                                <select class="form-select" id="prefecture_id" name="prefecture_id">
                                    <option value="">選択してください</option>
                                    <?php
                                    // 都道府県一覧を取得
                                    $prefectures = $db->fetchAll("SELECT id, name FROM prefectures ORDER BY id");
                                    foreach ($prefectures as $prefecture):
                                    ?>
                                        <option value="<?php echo $prefecture['id']; ?>" 
                                                <?php echo (isset($_POST['prefecture_id']) && $_POST['prefecture_id'] == $prefecture['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($prefecture['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city_id" class="form-label">
                                    <i class="fas fa-city me-1"></i>市区町村
                                </label>
                                <select class="form-select" id="city_id" name="city_id">
                                    <option value="">選択してください</option>
                                    <?php
                                    // 市区町村一覧を取得
                                    $cities = $db->fetchAll("SELECT id, name FROM cities ORDER BY name");
                                    foreach ($cities as $city):
                                    ?>
                                        <option value="<?php echo $city['id']; ?>" 
                                                <?php echo (isset($_POST['city_id']) && $_POST['city_id'] == $city['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($city['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-home me-1"></i>住所
                                </label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="町名・番地・建物名"
                                       value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                <a href="?page=terms" target="_blank">利用規約</a>と
                                <a href="?page=privacy" target="_blank">プライバシーポリシー</a>に同意します *
                            </label>
                            <div class="invalid-feedback">
                                利用規約に同意してください。
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>アカウント作成
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">
                            既にアカウントをお持ちの方は
                            <a href="?page=login" class="text-decoration-none fw-bold">
                                ログイン
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + 'ToggleIcon');
    
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

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>



