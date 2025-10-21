<?php
$page_title = '店舗登録';
$page_description = 'コンカフェの店舗登録を行います。';

// 店舗登録処理
if ($_POST && isset($_POST['register'])) {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $address = sanitize_input($_POST['address']);
    $prefecture_id = (int)$_POST['prefecture_id'];
    $city_id = (int)$_POST['city_id'];
    $phone = sanitize_input($_POST['phone']);
    $email = sanitize_input($_POST['email']);
    $website = sanitize_input($_POST['website']);
    $opening_hours = sanitize_input($_POST['opening_hours']);
    $concept_type = sanitize_input($_POST['concept_type']);
    $uniform_type = sanitize_input($_POST['uniform_type']);
    
    // 店舗管理者情報
    $admin_username = sanitize_input($_POST['admin_username']);
    $admin_email = sanitize_input($_POST['admin_email']);
    $admin_password = $_POST['admin_password'];
    $admin_password_confirm = $_POST['admin_password_confirm'];
    
    $errors = [];
    
    // バリデーション
    if (empty($name)) $errors[] = '店舗名を入力してください';
    if (empty($description)) $errors[] = '店舗説明を入力してください';
    if (empty($address)) $errors[] = '住所を入力してください';
    if (!$prefecture_id) $errors[] = '都道府県を選択してください';
    if (empty($phone)) $errors[] = '電話番号を入力してください';
    if (empty($email) || !validate_email($email)) $errors[] = '有効なメールアドレスを入力してください';
    if (empty($admin_username)) $errors[] = '管理者ユーザー名を入力してください';
    if (empty($admin_email) || !validate_email($admin_email)) $errors[] = '有効な管理者メールアドレスを入力してください';
    if (empty($admin_password)) $errors[] = 'パスワードを入力してください';
    if ($admin_password !== $admin_password_confirm) $errors[] = 'パスワードが一致しません';
    
    // ユニークチェック
    if ($db->fetch("SELECT id FROM shops WHERE name = ?", [$name])) {
        $errors[] = 'この店舗名は既に登録されています';
    }
    if ($db->fetch("SELECT id FROM shop_admins WHERE username = ?", [$admin_username])) {
        $errors[] = 'このユーザー名は既に使用されています';
    }
    if ($db->fetch("SELECT id FROM shop_admins WHERE email = ?", [$admin_email])) {
        $errors[] = 'このメールアドレスは既に使用されています';
    }
    
    if (empty($errors)) {
        try {
            $db->getConnection()->beginTransaction();
            
            // 店舗データを挿入
            $shop_id = $db->query(
                "INSERT INTO shops (name, description, address, prefecture_id, city_id, phone, email, website, 
                                   opening_hours, concept_type, uniform_type, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())",
                [$name, $description, $address, $prefecture_id, $city_id, $phone, $email, $website, 
                 $opening_hours, $concept_type, $uniform_type]
            )->lastInsertId();
            
            // 店舗管理者データを挿入
            $password_hash = hash_password($admin_password);
            $db->query(
                "INSERT INTO shop_admins (shop_id, username, email, password_hash, status, created_at) 
                 VALUES (?, ?, ?, ?, 'active', NOW())",
                [$shop_id, $admin_username, $admin_email, $password_hash]
            );
            
            $db->getConnection()->commit();
            
            $_SESSION['success_message'] = '店舗登録が完了しました。管理者による承認をお待ちください。';
            header('Location: ?page=shop_login');
            exit;
            
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $errors[] = '登録中にエラーが発生しました: ' . $e->getMessage();
        }
    }
}

// 都道府県データの取得
$prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id");

ob_start();
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-store me-2"></i>店舗登録
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
                        <!-- 店舗基本情報 -->
                        <h5 class="mb-3">
                            <i class="fas fa-store me-2"></i>店舗基本情報
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">店舗名 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="concept_type" class="form-label">コンセプト <span class="text-danger">*</span></label>
                                <select class="form-select" id="concept_type" name="concept_type" required>
                                    <option value="">選択してください</option>
                                    <option value="maid" <?php echo ($_POST['concept_type'] ?? '') == 'maid' ? 'selected' : ''; ?>>メイドカフェ</option>
                                    <option value="butler" <?php echo ($_POST['concept_type'] ?? '') == 'butler' ? 'selected' : ''; ?>>執事喫茶</option>
                                    <option value="idol" <?php echo ($_POST['concept_type'] ?? '') == 'idol' ? 'selected' : ''; ?>>アイドルカフェ</option>
                                    <option value="cosplay" <?php echo ($_POST['concept_type'] ?? '') == 'cosplay' ? 'selected' : ''; ?>>コスプレカフェ</option>
                                    <option value="other" <?php echo ($_POST['concept_type'] ?? '') == 'other' ? 'selected' : ''; ?>>その他</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">店舗説明 <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="prefecture_id" class="form-label">都道府県 <span class="text-danger">*</span></label>
                                <select class="form-select" id="prefecture_id" name="prefecture_id" required>
                                    <option value="">選択してください</option>
                                    <?php foreach ($prefectures as $prefecture): ?>
                                        <option value="<?php echo $prefecture['id']; ?>" 
                                                <?php echo ($_POST['prefecture_id'] ?? '') == $prefecture['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($prefecture['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="city_id" class="form-label">市区町村</label>
                                <input type="text" class="form-control" id="city_id" name="city_id" 
                                       value="<?php echo htmlspecialchars($_POST['city_id'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">住所 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">電話番号 <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="website" class="form-label">ウェブサイト</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="uniform_type" class="form-label">制服・衣装</label>
                                <input type="text" class="form-control" id="uniform_type" name="uniform_type" 
                                       value="<?php echo htmlspecialchars($_POST['uniform_type'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="opening_hours" class="form-label">営業時間</label>
                            <textarea class="form-control" id="opening_hours" name="opening_hours" rows="3"><?php echo htmlspecialchars($_POST['opening_hours'] ?? ''); ?></textarea>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- 管理者情報 -->
                        <h5 class="mb-3">
                            <i class="fas fa-user-shield me-2"></i>管理者情報
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="admin_username" class="form-label">管理者ユーザー名 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="admin_username" name="admin_username" 
                                       value="<?php echo htmlspecialchars($_POST['admin_username'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_email" class="form-label">管理者メールアドレス <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                       value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="admin_password" class="form-label">パスワード <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_password_confirm" class="form-label">パスワード確認 <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="admin_password_confirm" name="admin_password_confirm" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    <a href="?page=terms" target="_blank">利用規約</a>および
                                    <a href="?page=privacy" target="_blank">プライバシーポリシー</a>に同意します
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="register" class="btn btn-primary btn-lg">
                                <i class="fas fa-store me-2"></i>店舗を登録
                            </button>
                            <a href="?page=shop_login" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-in-alt me-2"></i>既に登録済みの方はこちら
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
