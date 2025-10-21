<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ファイル読み込み
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// 管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

$page_title = '管理者ダッシュボード';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- 管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index_simple.php">
                <i class="fas fa-shield-alt me-2"></i>管理者パネル
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../?page=admin_login">
                    <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">
                    <i class="fas fa-tachometer-alt me-2"></i>ダッシュボード
                </h1>
            </div>
        </div>
        
        <!-- 統計カード -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    総ユーザー数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                    try {
                                        $total_users = $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'];
                                        echo number_format($total_users);
                                    } catch (Exception $e) {
                                        echo 'エラー';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    総店舗数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                    try {
                                        $total_shops = $db->fetch("SELECT COUNT(*) as count FROM shops")['count'];
                                        echo number_format($total_shops);
                                    } catch (Exception $e) {
                                        echo 'エラー';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    総求人数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                    try {
                                        $total_jobs = $db->fetch("SELECT COUNT(*) as count FROM jobs")['count'];
                                        echo number_format($total_jobs);
                                    } catch (Exception $e) {
                                        echo 'エラー';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    総応募数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                    try {
                                        $total_applications = $db->fetch("SELECT COUNT(*) as count FROM applications")['count'];
                                        echo number_format($total_applications);
                                    } catch (Exception $e) {
                                        echo 'エラー';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ナビゲーションメニュー -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">管理機能</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="shops.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-store me-2"></i>店舗管理
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="jobs.php" class="btn btn-outline-info w-100">
                                    <i class="fas fa-briefcase me-2"></i>求人管理
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="users.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-users me-2"></i>ユーザー管理
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="applications.php" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-file-alt me-2"></i>応募管理
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
