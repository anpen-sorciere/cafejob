<?php
session_start();
require_once 'includes.php';

// 管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

// ログアウト処理
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ../?page=admin_login');
    exit;
}

// ログアウトページ
$page_title = 'ログアウト';
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <i class="fas fa-sign-out-alt fa-3x text-primary mb-3"></i>
                    <h3 class="mb-3">ログアウトしました</h3>
                    <p class="text-muted mb-4">
                        管理者パネルからログアウトしました。<br>
                        再度ログインするには、下のボタンをクリックしてください。
                    </p>
                    <a href="../?page=admin_login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>ログイン画面に戻る
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
