<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes.php';

// 店舗管理者認証チェック
if (!is_shop_admin()) {
    header('Location: ../?page=shop_admin_login');
    exit;
}

// 住所確認が必要な場合は確認ページにリダイレクト
if ($_SESSION['shop_status'] === 'verification_pending') {
    header('Location: verify_address.php');
    exit;
}

$page_title = '店舗情報編集';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// 店舗情報の取得
$shop_info = $db->fetch(
    "SELECT s.*, p.name as prefecture_name, c.name as city_name
     FROM shops s
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE s.id = ?",
    [$shop_id]
);

if (!$shop_info) {
    header('Location: dashboard.php');
    exit;
}

// 都道府県一覧の取得
$prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY name");

// 店舗情報更新処理
if ($_POST && isset($_POST['update_shop'])) {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $postal_code = sanitize_input($_POST['postal_code']);
    $address = sanitize_input($_POST['address']);
    $prefecture_id = (int)$_POST['prefecture_id'];
    $city_name = sanitize_input($_POST['city_name']);
    $phone = sanitize_input($_POST['phone']);
    $email = sanitize_input($_POST['email']);
    $website = sanitize_input($_POST['website']);
    $opening_hours = sanitize_input($_POST['opening_hours']);
    $concept_type = sanitize_input($_POST['concept_type']);
    $uniform_type = sanitize_input($_POST['uniform_type']);
    
    $errors = [];
    
    // バリデーション
    if (empty($name)) $errors[] = '店舗名を入力してください';
    if (empty($description)) $errors[] = '店舗説明を入力してください';
    if (empty($postal_code)) $errors[] = '郵便番号を入力してください';
    if (empty($city_name)) $errors[] = '市区町村名を入力してください';
    if (empty($address)) $errors[] = '住所を入力してください';
    if (empty($phone)) $errors[] = '電話番号を入力してください';
    if (empty($email)) $errors[] = 'メールアドレスを入力してください';
    if (empty($opening_hours)) $errors[] = '営業時間を入力してください';
    
    if (empty($errors)) {
        try {
            // 郵便番号を7桁の文字列として保存（先頭の0を保持）
            $postal_code_clean = str_replace('-', '', $postal_code);
            $postal_code_padded = str_pad($postal_code_clean, 7, '0', STR_PAD_LEFT);
            
            // 完全な住所を構築（市区町村名 + 詳細住所）
            $full_address = $city_name . $address;
            
            // 住所変更の検知
            $address_changed = false;
            $old_address_data = [
                'postal_code' => $shop_info['postal_code'],
                'prefecture_id' => $shop_info['prefecture_id'],
                'city_name' => $shop_info['city_name'],
                'address' => $shop_info['address']
            ];
            
            $new_address_data = [
                'postal_code' => $postal_code_padded,
                'prefecture_id' => $prefecture_id,
                'city_name' => $city_name,
                'address' => $full_address
            ];
            
            // 住所が変更されたかチェック
            if ($old_address_data['postal_code'] !== $new_address_data['postal_code'] ||
                $old_address_data['prefecture_id'] !== $new_address_data['prefecture_id'] ||
                $old_address_data['city_name'] !== $new_address_data['city_name'] ||
                $old_address_data['address'] !== $new_address_data['address']) {
                $address_changed = true;
            }
            
            if ($address_changed) {
                // 住所変更履歴を記録
                $verification_code = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                
                $db->query(
                    "INSERT INTO shop_address_changes 
                     (shop_id, old_postal_code, old_prefecture_id, old_city_name, old_address,
                      new_postal_code, new_prefecture_id, new_city_name, new_address, verification_code, verification_sent_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                    [$shop_id, $old_address_data['postal_code'], $old_address_data['prefecture_id'], 
                     $old_address_data['city_name'], $old_address_data['address'],
                     $new_address_data['postal_code'], $new_address_data['prefecture_id'],
                     $new_address_data['city_name'], $new_address_data['address'], $verification_code]
                );
                
                // 店舗の住所確認状態をロックに変更
                $db->query(
                    "UPDATE shops SET address_verification_status = 'locked', address_verification_locked_at = NOW() WHERE id = ?",
                    [$shop_id]
                );
                
                $_SESSION['success_message'] = '住所が変更されました。郵便による住所確認が必要です。確認コード: ' . $verification_code;
                $_SESSION['address_verification_pending'] = true;
                header('Location: verify_address.php');
                exit;
            } else {
                // 住所変更がない場合は通常の更新
                $db->query(
                    "UPDATE shops SET name = ?, description = ?, postal_code = ?, address = ?, 
                                    prefecture_id = ?, city_id = NULL, phone = ?, email = ?, website = ?, 
                                    opening_hours = ?, concept_type = ?, uniform_type = ?, updated_at = NOW() 
                     WHERE id = ?",
                    [$name, $description, $postal_code_padded, $full_address, $prefecture_id, 
                     $phone, $email, $website, $opening_hours, $concept_type, $uniform_type, $shop_id]
                );
                
                $_SESSION['success_message'] = '店舗情報を更新しました。';
                header('Location: shop_info.php');
                exit;
            }
            
        } catch (Exception $e) {
            $errors[] = '店舗情報更新中にエラーが発生しました: ' . $e->getMessage();
        }
    }
}

ob_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo htmlspecialchars($shop_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- 店舗管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-store me-2"></i><?php echo htmlspecialchars($shop_name); ?> 管理パネル
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>ダッシュボード
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="shop_info.php">
                            <i class="fas fa-store me-1"></i>店舗情報
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">
                            <i class="fas fa-briefcase me-1"></i>求人管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="applications.php">
                            <i class="fas fa-file-alt me-1"></i>応募管理
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars($_SESSION['shop_admin_username'] ?? '管理者'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>ログアウト</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h2 class="h4 mb-0">
                            <i class="fas fa-store me-2"></i>店舗情報編集
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

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($shop_info['address_verification_status'] === 'locked'): ?>
                            <div class="alert alert-warning">
                                <h5 class="alert-heading">
                                    <i class="fas fa-lock me-2"></i>住所確認待ち
                                </h5>
                                <p class="mb-3">店舗の住所が変更されました。郵便による住所確認が完了するまで、一部機能が制限されます。</p>
                                <a href="verify_address.php" class="btn btn-warning">
                                    <i class="fas fa-mail-bulk me-1"></i>住所確認ページへ
                                </a>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <!-- 基本情報 -->
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>基本情報
                            </h5>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">店舗名 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="例: カフェ・ドリーム"
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? $shop_info['name']); ?>" required>
                                <div class="invalid-feedback">店舗名を入力してください</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">店舗説明 <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required
                                          placeholder="店舗の特徴や魅力を詳しく記載してください"><?php echo htmlspecialchars($_POST['description'] ?? $shop_info['description']); ?></textarea>
                                <div class="invalid-feedback">店舗説明を入力してください</div>
                            </div>

                            <!-- 住所情報 -->
                            <h5 class="mb-3 mt-4">
                                <i class="fas fa-map-marker-alt me-2"></i>住所情報
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label">郵便番号 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                           placeholder="例: 100-0001" maxlength="8"
                                           value="<?php echo htmlspecialchars($_POST['postal_code'] ?? (!empty($shop_info['postal_code']) ? substr($shop_info['postal_code'], 0, 3) . '-' . substr($shop_info['postal_code'], 3) : '')); ?>" required>
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
                                        <?php foreach ($prefectures as $pref): ?>
                                            <option value="<?php echo $pref['id']; ?>" 
                                                <?php echo (isset($_POST['prefecture_id']) && $_POST['prefecture_id'] == $pref['id']) ? 'selected' : 
                                                    ($shop_info['prefecture_id'] == $pref['id'] ? 'selected' : ''); ?>>
                                                <?php echo htmlspecialchars($pref['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">都道府県を選択してください</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="city_name" class="form-label">市区町村 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city_name" name="city_name" 
                                           placeholder="例: 大阪市浪速区"
                                           value="<?php echo htmlspecialchars($_POST['city_name'] ?? $shop_info['city_name']); ?>" required>
                                    <div class="invalid-feedback">市区町村名を入力してください</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">住所 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="例: なんば1-2-3 なんばビル4F"
                                       value="<?php echo htmlspecialchars($_POST['address'] ?? $shop_info['address']); ?>" required>
                                <div class="invalid-feedback">住所を入力してください</div>
                            </div>

                            <!-- 連絡先情報 -->
                            <h5 class="mb-3 mt-4">
                                <i class="fas fa-phone me-2"></i>連絡先情報
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">電話番号 <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           placeholder="例: 06-1234-5678"
                                           value="<?php echo htmlspecialchars($_POST['phone'] ?? $shop_info['phone']); ?>" required>
                                    <div class="invalid-feedback">電話番号を入力してください</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="例: info@example.com"
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? $shop_info['email']); ?>" required>
                                    <div class="invalid-feedback">メールアドレスを入力してください</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="website" class="form-label">ウェブサイト</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       placeholder="例: https://example.com"
                                       value="<?php echo htmlspecialchars($_POST['website'] ?? $shop_info['website']); ?>">
                            </div>

                            <!-- 営業情報 -->
                            <h5 class="mb-3 mt-4">
                                <i class="fas fa-clock me-2"></i>営業情報
                            </h5>

                            <div class="mb-3">
                                <label for="opening_hours" class="form-label">営業時間 <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="opening_hours" name="opening_hours" rows="3" required
                                          placeholder="例: 平日 10:00-22:00&#10;土日祝 9:00-23:00"><?php echo htmlspecialchars($_POST['opening_hours'] ?? $shop_info['opening_hours']); ?></textarea>
                                <div class="invalid-feedback">営業時間を入力してください</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="concept_type" class="form-label">コンセプト</label>
                                    <select class="form-select" id="concept_type" name="concept_type">
                                        <option value="other" <?php echo ($_POST['concept_type'] ?? $shop_info['concept_type']) === 'other' ? 'selected' : ''; ?>>その他</option>
                                        <option value="maid" <?php echo ($_POST['concept_type'] ?? $shop_info['concept_type']) === 'maid' ? 'selected' : ''; ?>>メイド</option>
                                        <option value="butler" <?php echo ($_POST['concept_type'] ?? $shop_info['concept_type']) === 'butler' ? 'selected' : ''; ?>>バトラー</option>
                                        <option value="idol" <?php echo ($_POST['concept_type'] ?? $shop_info['concept_type']) === 'idol' ? 'selected' : ''; ?>>アイドル</option>
                                        <option value="cosplay" <?php echo ($_POST['concept_type'] ?? $shop_info['concept_type']) === 'cosplay' ? 'selected' : ''; ?>>コスプレ</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="uniform_type" class="form-label">制服タイプ</label>
                                    <input type="text" class="form-control" id="uniform_type" name="uniform_type" 
                                           placeholder="例: メイド服、制服など"
                                           value="<?php echo htmlspecialchars($_POST['uniform_type'] ?? $shop_info['uniform_type']); ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update_shop" class="btn btn-info btn-lg">
                                    <i class="fas fa-save me-2"></i>店舗情報を更新
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>ダッシュボードに戻る
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const postalCodeInput = document.getElementById('postal_code');
        const searchButton = document.getElementById('search_address');
        const prefectureSelect = document.getElementById('prefecture_id');
        const cityInput = document.getElementById('city_name');
        const addressInput = document.getElementById('address');

        // 郵便番号の自動補完機能
        searchButton.addEventListener('click', function() {
            const postalCode = postalCodeInput.value.replace(/[^0-9]/g, '');
            
            if (postalCode.length !== 7) {
                alert('郵便番号は7桁で入力してください');
                return;
            }

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
                        
                        // 住所フィールドに町域を設定
                        addressInput.value = result.address3;
                        
                        // 成功メッセージ
                        showMessage('住所を自動補完しました。番地・建物名を追加してください。', 'success');
                    } else {
                        showMessage('郵便番号が見つかりませんでした。', 'warning');
                    }
                })
                .catch(error => {
                    showMessage('住所検索中にエラーが発生しました。', 'danger');
                });
        });

        // Bootstrapバリデーション
        const form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // メッセージ表示関数
        function showMessage(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.insertBefore(alertDiv, form.firstChild);
            
            // 3秒後に自動で消す
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    });
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
