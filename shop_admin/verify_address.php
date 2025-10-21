<?php
session_start();

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
    if ($_SESSION['shop_status'] !== 'verification_pending') {
        custom_error_log('verify_address.php - Shop status is not verification_pending, redirecting to dashboard');
        header('Location: ../?page=shop_dashboard');
        exit;
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
    } elseif (strlen($input_code) !== 6 || !is_numeric($input_code)) {
        $error_message = '確認コードは6桁の数字で入力してください。';
    } else {
        // 店舗の確認コードを取得
        $shop = $db->fetch("SELECT verification_code FROM shops WHERE id = ?", [$shop_id]);
        
        if ($shop && $shop['verification_code'] === $input_code) {
            // 確認コードが正しい場合、店舗ステータスをactiveに更新
            $db->query(
                "UPDATE shops SET status = 'active', verification_verified_at = NOW() WHERE id = ?",
                [$shop_id]
            );
            
            $_SESSION['shop_status'] = 'active';
            $_SESSION['success_message'] = '住所確認が完了しました。店舗がアクティブになりました。';
            header('Location: ../?page=shop_dashboard');
            exit;
        } else {
            $error_message = '確認コードが正しくありません。郵便に記載された6桁の数字を正確に入力してください。';
        }
    }
}

// 店舗情報を取得（都道府県名も含む）
$shop_info = $db->fetch(
    "SELECT s.address, s.verification_code, p.name as prefecture_name, c.name as city_name
     FROM shops s
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE s.id = ?", 
    [$shop_id]
);

// 完全な住所を構築
$full_address = '';
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
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>住所確認について
                            </h5>
                            <p class="mb-0">
                                店舗登録時にご入力いただいた住所に、6桁の確認コードを記載した郵便を送信いたしました。
                                郵便が届きましたら、下記フォームに確認コードを入力してください。
                            </p>
                        </div>

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
                                       placeholder="123456" maxlength="6" pattern="[0-9]{6}" required>
                                <div class="form-text">
                                    郵便に記載された6桁の数字を入力してください
                                </div>
                                <div class="invalid-feedback">
                                    6桁の数字を入力してください
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="verify_code" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle me-2"></i>住所確認を完了
                                </button>
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
                                    <li>住所確認が完了するまで求人の投稿はできません</li>
                                    <li>確認コードが分からない場合は、お問い合わせください</li>
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
