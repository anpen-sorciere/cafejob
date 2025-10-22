<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 独自エラーログシステムを読み込み
require_once '../includes/error_logger.php';

custom_error_log('verify_address.php - Starting execution');

try {
    require_once '../config/config.php';
    custom_error_log('verify_address.php - config.php loaded');
    
    require_once '../includes/functions.php';
    custom_error_log('verify_address.php - functions.php loaded');
    
    // デバッグ用：セッション情報を確認
    custom_error_log('verify_address.php - Session data', $_SESSION);
    
    // 店舗管理者認証チェック
    custom_error_log('verify_address.php - Calling require_shop_admin()');
    require_shop_admin();
    custom_error_log('verify_address.php - require_shop_admin() completed');

    // 住所確認が必要でない場合はダッシュボードにリダイレクト
    if ($_SESSION['shop_status'] !== 'verification_pending' && !isset($_SESSION['address_verification_pending'])) {
        custom_error_log('verify_address.php - Shop status is not verification_pending, redirecting to dashboard');
        header('Location: dashboard.php');
        exit;
    }
    
    // 住所変更がロックされているかチェック
    $locked_address_change = $db->fetch(
        "SELECT id FROM shop_address_changes 
         WHERE shop_id = ? AND status = 'pending' AND is_locked = TRUE 
         ORDER BY created_at DESC LIMIT 1",
        [$shop_id]
    );
    
    if ($locked_address_change) {
        $_SESSION['address_verification_pending'] = true;
    }

    $page_title = '住所確認';
    $shop_id = get_shop_admin_shop_id();
    $shop_name = get_shop_admin_shop_name();

    // セッション情報が不足している場合はログインページにリダイレクト
    if (!$shop_id || !$shop_name) {
        custom_error_log('verify_address.php - Missing shop_id or shop_name, redirecting to shop_login');
        header('Location: ../?page=shop_login');
        exit;
    }

// 確認コードの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $input_code = sanitize_input($_POST['verification_code']);
    
    if (empty($input_code)) {
        $error_message = '確認コードを入力してください。';
    } else {
        // 現在のIPアドレスとユーザーエージェントを取得
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // 住所変更の確認コードをチェック
        $address_change = $db->fetch(
            "SELECT * FROM shop_address_changes 
             WHERE shop_id = ? AND status = 'pending' AND is_locked = FALSE
             ORDER BY created_at DESC LIMIT 1",
            [$shop_id]
        );
        
        if ($address_change) {
            // 入力ミス履歴を記録
            $db->query(
                "INSERT INTO verification_attempts 
                 (shop_id, attempt_type, verification_code, input_code, ip_address, user_agent, is_successful, attempt_time)
                 VALUES (?, 'address_change', ?, ?, ?, ?, ?, NOW())",
                [$shop_id, $address_change['verification_code'], $input_code, $ip_address, $user_agent, 
                 ($input_code === $address_change['verification_code'])]
            );
            
            if ($input_code === $address_change['verification_code']) {
                // 確認コードが正しい場合
                $db->query(
                    "UPDATE shop_address_changes SET status = 'verified', verified_at = NOW() WHERE id = ?",
                    [$address_change['id']]
                );
                
                // 店舗の住所を新しい住所に更新
                $db->query(
                    "UPDATE shops SET 
                     postal_code = ?, prefecture_id = ?, address = ?, 
                     address_verification_status = 'verified', address_verification_locked_at = NULL
                     WHERE id = ?",
                    [$address_change['new_postal_code'], $address_change['new_prefecture_id'], 
                     $address_change['new_address'], $shop_id]
                );
                
                // セッションをクリア
                unset($_SESSION['address_verification_pending']);
                
                $_SESSION['success_message'] = '住所確認が完了しました。新しい住所が有効になりました。';
                header('Location: shop_info.php');
                exit;
            } else {
                // 確認コードが間違っている場合
                $failed_attempts = $address_change['failed_attempts'] + 1;
                
                if ($failed_attempts >= 3) {
                    // 3回ミスした場合はロック
                    $db->query(
                        "UPDATE shop_address_changes SET failed_attempts = ?, is_locked = TRUE, locked_at = NOW() WHERE id = ?",
                        [$failed_attempts, $address_change['id']]
                    );
                    
                    $error_message = '確認コードの入力ミスが3回に達しました。セキュリティのため、この住所変更はロックされました。運営に問い合わせてください。';
                } else {
                    // 失敗回数を更新
                    $db->query(
                        "UPDATE shop_address_changes SET failed_attempts = ? WHERE id = ?",
                        [$failed_attempts, $address_change['id']]
                    );
                    
                    $remaining_attempts = 3 - $failed_attempts;
                    $error_message = "確認コードが正しくありません。残り{$remaining_attempts}回の試行が可能です。";
                }
            }
        } else {
            // 従来の店舗登録時の確認コードもチェック
            $shop = $db->fetch("SELECT verification_code FROM shops WHERE id = ?", [$shop_id]);
            
            if ($shop && $shop['verification_code'] === $input_code) {
                // 確認コードが正しい場合、店舗ステータスをactiveに更新
                $db->query(
                    "UPDATE shops SET status = 'active', verification_verified_at = NOW() WHERE id = ?",
                    [$shop_id]
                );
                
                $_SESSION['shop_status'] = 'active';
                $_SESSION['success_message'] = '住所確認が完了しました。店舗がアクティブになりました。';
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = '確認コードが正しくありません。郵便に記載されたコードを正確に入力してください。';
            }
        }
    }
}

// 店舗情報を取得（都道府県名と郵便番号も含む）
$shop_info = $db->fetch(
    "SELECT s.address, s.verification_code, s.postal_code, p.name as prefecture_name, c.name as city_name
     FROM shops s
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE s.id = ?", 
    [$shop_id]
);

// 住所変更情報を取得
$address_change_info = null;
$is_locked = false;
if (isset($_SESSION['address_verification_pending'])) {
    $address_change_info = $db->fetch(
        "SELECT * FROM shop_address_changes 
         WHERE shop_id = ? AND status = 'pending' 
         ORDER BY created_at DESC LIMIT 1",
        [$shop_id]
    );
    
    if ($address_change_info && $address_change_info['is_locked']) {
        $is_locked = true;
    }
}

// 完全な住所を構築
$full_address = '';
if (!empty($shop_info['postal_code'])) {
    $postal_code_str = str_pad($shop_info['postal_code'], 7, '0', STR_PAD_LEFT);
    $formatted_postal_code = substr($postal_code_str, 0, 3) . '-' . substr($postal_code_str, 3);
    $full_address .= '〒' . $formatted_postal_code . ' ';
}
if (!empty($shop_info['prefecture_name'])) {
    $full_address .= $shop_info['prefecture_name'];
}
if (!empty($shop_info['city_name'])) {
    $full_address .= $shop_info['city_name'];
}
if (!empty($shop_info['address'])) {
    $full_address .= $shop_info['address'];
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
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h2 class="h4 mb-0 text-center">
                            <i class="fas fa-mail-bulk me-2"></i>住所確認
                        </h2>
                    </div>
                    <div class="card-body p-5">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($address_change_info): ?>
                            <?php if ($is_locked): ?>
                                <!-- ロック状態の表示 -->
                                <div class="alert alert-danger">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-lock me-2"></i>住所変更がロックされました
                                    </h5>
                                    <p class="mb-3">確認コードの入力ミスが3回に達したため、セキュリティのためこの住所変更はロックされました。</p>
                                    <p class="mb-0"><strong>お問い合わせ:</strong> 運営に問い合わせてください。</p>
                                </div>
                            <?php else: ?>
                                <!-- 住所変更時の確認 -->
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-map-marker-alt me-2"></i>住所変更の確認
                                    </h5>
                                    <p class="mb-3">店舗の住所が変更されました。新しい住所に確認用の郵便を送信しましたので、郵便に記載された6桁の確認コードを入力してください。</p>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">変更前の住所</h6>
                                            <p class="mb-2">
                                                〒<?php echo substr($address_change_info['old_postal_code'], 0, 3) . '-' . substr($address_change_info['old_postal_code'], 3); ?><br>
                                                <?php echo htmlspecialchars($address_change_info['old_address']); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">変更後の住所</h6>
                                            <p class="mb-2">
                                                〒<?php echo substr($address_change_info['new_postal_code'], 0, 3) . '-' . substr($address_change_info['new_postal_code'], 3); ?><br>
                                                <?php echo htmlspecialchars($address_change_info['new_address']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning mt-3">
                                        <strong>注意:</strong> 新しい住所に郵便を送信しました。郵便に記載された6桁の確認コードを入力してください。
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- 通常の住所確認 -->
                            <div class="alert alert-info">
                                <h5 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>住所確認について
                                </h5>
                                <p class="mb-0">
                                    店舗登録時にご入力いただいた住所に、6桁の確認コードを記載した郵便を送信いたしました。
                                    郵便が届きましたら、下記フォームに確認コードを入力してください。
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h6 class="text-muted">送信先住所</h6>
                            <p class="fw-bold"><?php echo htmlspecialchars($full_address); ?></p>
                        </div>

                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="verification_code" class="form-label">
                                    <i class="fas fa-key me-1"></i>確認コード <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg text-center" 
                                       id="verification_code" name="verification_code" 
                                       placeholder="123456" maxlength="6" pattern="[0-9]{6}" required
                                       <?php echo $is_locked ? 'disabled' : ''; ?>>
                                <div class="form-text">
                                    郵便に記載された6桁の数字を入力してください
                                </div>
                                <div class="invalid-feedback">
                                    6桁の数字を入力してください
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <?php if ($is_locked): ?>
                                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                                        <i class="fas fa-lock me-2"></i>ロック中
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="verify_code" class="btn btn-success btn-lg">
                                        <i class="fas fa-check-circle me-2"></i>住所確認を完了
                                    </button>
                                <?php endif; ?>
                                <a href="logout.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                </a>
                            </div>
                        </form>

                        <div class="mt-4">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>注意事項
                                </h6>
                                <ul class="mb-0">
                                    <li>確認コードは郵便が届いてから入力してください</li>
                                    <li>住所確認が完了するまで一部機能が制限されます</li>
                                    <li>確認コードが分からない場合は、お問い合わせください</li>
                                    <li>確認コードは郵便物にのみ記載されています</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('verification_code');
        
        // 数字のみ入力可能
        codeInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            // 6桁を超えた場合は切り詰める
            if (e.target.value.length > 6) {
                e.target.value = e.target.value.substring(0, 6);
            }
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
    });
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
echo $content;

} catch (Exception $e) {
    custom_error_log('verify_address.php - Exception caught', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // エラーが発生した場合はログインページにリダイレクト
    header('Location: ../?page=shop_login');
    exit;
}
?>
