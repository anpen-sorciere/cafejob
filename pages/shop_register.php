<?php
$page_title = '店舗登録';
$page_description = 'コンカフェの店舗登録を行います。';

// 店舗登録処理
if ($_POST && isset($_POST['register'])) {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $postal_code = sanitize_input($_POST['postal_code']);
    $address = sanitize_input($_POST['address']);
    $prefecture_id = (int)$_POST['prefecture_id'];
    $city_name = sanitize_input($_POST['city_id']); // 市区町村名をテキストとして取得
    $phone = sanitize_input($_POST['phone']);
    $email = sanitize_input($_POST['email']);
    $website = sanitize_input($_POST['website']);
    $opening_hours = sanitize_input($_POST['opening_hours']);
    $concept_type = sanitize_input($_POST['concept_type']);
    $uniform_type = sanitize_input($_POST['uniform_type']);
    
    // 店舗管理者情報
    $admin_last_name = sanitize_input($_POST['admin_last_name']);
    $admin_first_name = sanitize_input($_POST['admin_first_name']);
    $admin_email = sanitize_input($_POST['admin_email']);
    $admin_email_confirm = sanitize_input($_POST['admin_email_confirm']);
    $admin_password = $_POST['admin_password'];
    $admin_password_confirm = $_POST['admin_password_confirm'];
    
    $errors = [];
    
    // バリデーション
    if (empty($name)) $errors[] = '店舗名を入力してください';
    if (empty($description)) $errors[] = '店舗説明を入力してください';
    if (empty($postal_code)) $errors[] = '郵便番号を入力してください';
    if (empty($city_name)) $errors[] = '市区町村名を入力してください';
    if (empty($address)) $errors[] = '住所を入力してください';
    if (!$prefecture_id) $errors[] = '都道府県を選択してください';
    if (empty($phone)) $errors[] = '電話番号を入力してください';
    if (empty($email) || !validate_email($email)) $errors[] = '有効なメールアドレスを入力してください';
    if (empty($admin_last_name)) $errors[] = '姓を入力してください';
    if (empty($admin_first_name)) $errors[] = '名前を入力してください';
    if (empty($admin_email) || !validate_email($admin_email)) $errors[] = '有効なメールアドレスを入力してください';
    if (empty($admin_email_confirm) || !validate_email($admin_email_confirm)) $errors[] = '有効なメールアドレス確認を入力してください';
    if ($admin_email !== $admin_email_confirm) $errors[] = 'メールアドレスが一致しません';
    if (empty($admin_password)) $errors[] = 'パスワードを入力してください';
    if ($admin_password !== $admin_password_confirm) $errors[] = 'パスワードが一致しません';
    
    // ユニークチェック
    if ($db->fetch("SELECT id FROM shops WHERE name = ?", [$name])) {
        $errors[] = 'この店舗名は既に登録されています';
    }
    // 管理者のユーザー名は姓+名で自動生成
    $admin_username = $admin_last_name . $admin_first_name;
    if ($db->fetch("SELECT id FROM shop_admins WHERE email = ?", [$admin_email])) {
        $errors[] = 'このメールアドレスは既に使用されています';
    }
    
    if (empty($errors)) {
        try {
            $db->getConnection()->beginTransaction();
            
            // 6桁の確認コードを生成
            $verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // 郵便番号を7桁の文字列として保存（先頭の0を保持）
            $postal_code_clean = str_replace('-', '', $postal_code);
            $postal_code_padded = str_pad($postal_code_clean, 7, '0', STR_PAD_LEFT);
            
            // 完全な住所を構築（市区町村名 + 詳細住所）
            $full_address = $city_name . $address;
            $stmt = $db->query(
                "INSERT INTO shops (name, description, postal_code, address, prefecture_id, city_id, phone, email, website, 
                                   opening_hours, concept_type, uniform_type, status, verification_code, verification_sent_at, created_at) 
                 VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 'verification_pending', ?, NOW(), NOW())",
                [$name, $description, $postal_code_padded, $full_address, $prefecture_id, $phone, $email, $website, 
                 $opening_hours, $concept_type, $uniform_type, $verification_code]
            );
            $shop_id = $db->getConnection()->lastInsertId();
            
            // 店舗管理者データを挿入
            $password_hash = hash_password($admin_password);
            $db->query(
                "INSERT INTO shop_admins (shop_id, username, email, password_hash, status, created_at) 
                 VALUES (?, ?, ?, ?, 'active', NOW())",
                [$shop_id, $admin_username, $admin_email, $password_hash]
            );
            
            $db->getConnection()->commit();
            
            $_SESSION['success_message'] = '店舗登録が完了しました。住所確認のため、入力された住所に6桁の確認コード（' . $verification_code . '）を記載した郵便を送信いたします。郵便が届きましたら、店舗管理者ログイン後に確認コードを入力してください。';
            header('Location: ?page=shop_login');
            exit;
            
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $errors[] = '登録中にエラーが発生しました: ' . $e->getMessage();
            // デバッグ用：詳細なエラー情報をログに記録
            error_log('Shop registration error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
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
                    
                    <?php if (DEBUG_MODE && !empty($errors)): ?>
                        <div class="alert alert-warning">
                            <h6>デバッグ情報</h6>
                            <pre><?php echo htmlspecialchars(print_r($errors, true)); ?></pre>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- 店舗基本情報 -->
                        <h5 class="mb-3">
                            <i class="fas fa-store me-2"></i>店舗基本情報
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">店舗名 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                <div class="invalid-feedback">店舗名を入力してください</div>
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
                                <div class="invalid-feedback">コンセプトを選択してください</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">店舗説明 <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">店舗説明を入力してください</div>
                        </div>
                        
                        <!-- 郵便番号入力 -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">郵便番号</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       placeholder="例: 100-0001" maxlength="8"
                                       value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>">
                                <div class="form-text">郵便番号を入力すると住所が自動で補完されます</div>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary" id="search_address">
                                    <i class="fas fa-search me-1"></i>住所を検索
                                </button>
                            </div>
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
                                <div class="invalid-feedback">都道府県を選択してください</div>
                            </div>
                            <div class="col-md-6">
                                <label for="city_id" class="form-label">市区町村 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city_id" name="city_id" 
                                       placeholder="例: 大阪市浪速区"
                                       value="<?php echo htmlspecialchars($_POST['city_id'] ?? ''); ?>" required>
                                <div class="invalid-feedback">市区町村名を入力してください</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">住所（番地・建物名） <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   placeholder="例: 1-2-3 サンプルビル 4F"
                                   value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                            <div class="invalid-feedback">住所を入力してください</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">電話番号 <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                                <div class="invalid-feedback">電話番号を入力してください</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                <div class="invalid-feedback">有効なメールアドレスを入力してください</div>
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
                                <label for="admin_last_name" class="form-label">姓 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="admin_last_name" name="admin_last_name" 
                                       value="<?php echo htmlspecialchars($_POST['admin_last_name'] ?? ''); ?>" required>
                                <div class="invalid-feedback">姓を入力してください</div>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_first_name" class="form-label">名 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="admin_first_name" name="admin_first_name" 
                                       value="<?php echo htmlspecialchars($_POST['admin_first_name'] ?? ''); ?>" required>
                                <div class="invalid-feedback">名を入力してください</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="admin_email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                       value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>" required>
                                <div class="invalid-feedback">有効なメールアドレスを入力してください</div>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_email_confirm" class="form-label">メールアドレス確認 <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="admin_email_confirm" name="admin_email_confirm" 
                                       value="<?php echo htmlspecialchars($_POST['admin_email_confirm'] ?? ''); ?>" required>
                                <div class="invalid-feedback">メールアドレス確認を入力してください</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="admin_password" class="form-label">パスワード <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                <div class="invalid-feedback">パスワードを入力してください</div>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_password_confirm" class="form-label">パスワード確認 <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="admin_password_confirm" name="admin_password_confirm" required>
                                <div class="invalid-feedback">パスワード確認を入力してください</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const postalCodeInput = document.getElementById('postal_code');
    const prefectureSelect = document.getElementById('prefecture_id');
    const cityInput = document.getElementById('city_id');
    const searchButton = document.getElementById('search_address');
    
    // 郵便番号の自動フォーマット
    postalCodeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length >= 3) {
            value = value.substring(0, 3) + '-' + value.substring(3, 7);
        }
        e.target.value = value;
        
        // 7桁入力されたら自動検索
        if (value.length === 8) {
            searchAddress();
        }
    });
    
    // 住所検索ボタン
    searchButton.addEventListener('click', searchAddress);
    
    function searchAddress() {
        const postalCode = postalCodeInput.value.replace(/[^0-9]/g, '');
        
        if (postalCode.length !== 7) {
            alert('郵便番号は7桁で入力してください');
            return;
        }
        
        searchButton.disabled = true;
        searchButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>検索中...';
        
        // 郵便番号APIを使用して住所を取得
        fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postalCode}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.results && data.results.length > 0) {
                    const result = data.results[0];
                    
                    // 都道府県を設定
                    const prefectureName = result.address1;
                    for (let option of prefectureSelect.options) {
                        if (option.text.includes(prefectureName)) {
                            option.selected = true;
                            break;
                        }
                    }
                    
                    // 市区町村を設定
                    cityInput.value = result.address2;
                    
                    // 住所フィールドに町域を設定（番地は手動入力）
                    const addressInput = document.getElementById('address');
                    addressInput.value = result.address3;
                    addressInput.focus();
                    
                    // バリデーション状態を更新
                    cityInput.classList.remove('is-invalid');
                    cityInput.classList.add('is-valid');
                    addressInput.classList.remove('is-invalid');
                    addressInput.classList.add('is-valid');
                    
                    // 成功メッセージ
                    showMessage('住所を自動補完しました。番地・建物名を追加してください。', 'success');
                } else {
                    showMessage('郵便番号が見つかりませんでした。', 'warning');
                }
            })
            .catch(error => {
                console.error('住所検索エラー:', error);
                showMessage('住所検索中にエラーが発生しました。', 'danger');
            })
            .finally(() => {
                searchButton.disabled = false;
                searchButton.innerHTML = '<i class="fas fa-search me-1"></i>住所を検索';
            });
    }
    
    function showMessage(message, type) {
        // 既存のメッセージを削除
        const existingAlert = document.querySelector('.alert-address');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // 新しいメッセージを表示
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-address`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // フォームの上部に挿入
        const form = document.querySelector('form');
        form.insertBefore(alertDiv, form.firstChild);
        
        // 3秒後に自動で消す
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }
    
    // パスワード確認のリアルタイムチェック
    const passwordInput = document.getElementById('admin_password');
    const passwordConfirmInput = document.getElementById('admin_password_confirm');
    
    // メールアドレス確認のリアルタイムチェック
    const emailInput = document.getElementById('admin_email');
    const emailConfirmInput = document.getElementById('admin_email_confirm');
    
    function checkPasswordMatch() {
        if (passwordConfirmInput.value && passwordInput.value !== passwordConfirmInput.value) {
            passwordConfirmInput.setCustomValidity('パスワードが一致しません');
        } else {
            passwordConfirmInput.setCustomValidity('');
        }
    }
    
    function checkEmailMatch() {
        if (emailConfirmInput.value && emailInput.value !== emailConfirmInput.value) {
            emailConfirmInput.setCustomValidity('メールアドレスが一致しません');
        } else {
            emailConfirmInput.setCustomValidity('');
        }
    }
    
    passwordInput.addEventListener('input', checkPasswordMatch);
    passwordConfirmInput.addEventListener('input', checkPasswordMatch);
    emailInput.addEventListener('input', checkEmailMatch);
    emailConfirmInput.addEventListener('input', checkEmailMatch);
    
    // Bootstrapバリデーション
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
