<?php
// 既存セッションを完全に破棄してから新しいセッションを開始
if (session_status() !== PHP_SESSION_NONE) {
    session_destroy();
}

// セッション設定をリセット
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Lax');

// cafejob専用セッションを開始
session_name('cafejob_session');
session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// 店舗管理者認証チェック
if (!is_shop_admin()) {
    header('Location: ../?page=shop_admin_login');
    exit;
}

$db = new Database();
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'] ?? '店舗';

// エラーハンドリング付きでデータを取得
$stats = ['total_jobs' => 0, 'active_jobs' => 0, 'total_applications' => 0, 'pending_applications' => 0];
$recent_applications = [];
$active_jobs = [];

try {
    // 統計データの取得
    $stats = [
        'total_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE shop_id = ?", [$shop_id])['count'] ?? 0,
        'active_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE shop_id = ? AND status = 'active'", [$shop_id])['count'] ?? 0,
        'total_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.shop_id = ?", [$shop_id])['count'] ?? 0,
        'pending_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.shop_id = ? AND a.status = 'pending'", [$shop_id])['count'] ?? 0
    ];
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
}

try {
    // 最新の応募情報
    $recent_applications = $db->fetchAll(
        "SELECT a.*, j.title as job_title, u.username, u.email
         FROM applications a
         JOIN jobs j ON a.job_id = j.id
         JOIN users u ON a.user_id = u.id
         WHERE j.shop_id = ?
         ORDER BY a.applied_at DESC
         LIMIT 5",
        [$shop_id]
    );
} catch (Exception $e) {
    error_log("Dashboard recent applications error: " . $e->getMessage());
}

try {
    // アクティブな求人
    $active_jobs = $db->fetchAll(
        "SELECT j.*, COUNT(a.id) as application_count
         FROM jobs j
         LEFT JOIN applications a ON j.id = a.job_id
         WHERE j.shop_id = ? AND j.status = 'active'
         GROUP BY j.id
         ORDER BY j.created_at DESC
         LIMIT 5",
        [$shop_id]
    );
} catch (Exception $e) {
    error_log("Dashboard active jobs error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗管理ダッシュボード - カフェJob</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-coffee me-2"></i>カフェJob
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>ダッシュボード
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
                    <li class="nav-item">
                        <a class="nav-link" href="chat.php">
                            <i class="fas fa-comments me-1"></i>チャット
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop_info.php">
                            <i class="fas fa-store me-1"></i>店舗情報
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-store me-2"></i><?php echo htmlspecialchars($shop_name); ?> ダッシュボード
                    </h1>
                    <div>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                        </a>
                    </div>
                </div>
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
                                    総求人数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total_jobs']); ?>
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
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    公開中求人
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['active_jobs']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                                    総応募数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total_applications']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                                    未処理応募
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['pending_applications']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- クイックアクション -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>クイックアクション
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="job_create.php" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-1"></i>新規求人投稿
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="jobs.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-briefcase me-1"></i>求人管理
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="applications.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-file-alt me-1"></i>応募管理
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="shop_info.php" class="btn btn-outline-info w-100">
                                    <i class="fas fa-store me-1"></i>店舗情報編集
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 最新の応募 -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-alt me-2"></i>最新の応募
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_applications)): ?>
                            <p class="text-muted text-center">応募がありません</p>
                        <?php else: ?>
                            <?php foreach ($recent_applications as $application): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($application['job_title']); ?></h6>
                                        <p class="mb-1 small text-muted">
                                            <?php echo htmlspecialchars($application['username']); ?> | 
                                            <?php echo htmlspecialchars($application['email']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo date('Y-m-d H:i', strtotime($application['applied_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php 
                                            echo $application['status'] == 'pending' ? 'warning' : 
                                                ($application['status'] == 'accepted' ? 'success' : 'danger'); 
                                        ?>">
                                            <?php 
                                            $status_labels = [
                                                'pending' => '審査中',
                                                'accepted' => '採用',
                                                'rejected' => '不採用',
                                                'cancelled' => 'キャンセル'
                                            ];
                                            echo $status_labels[$application['status']] ?? $application['status'];
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-3">
                                <a href="applications.php" class="btn btn-outline-primary">
                                    すべての応募を見る
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- 求人一覧 -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-briefcase me-2"></i>求人一覧
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($active_jobs)): ?>
                            <p class="text-muted text-center">求人がありません</p>
                            <div class="text-center">
                                <a href="job_create.php" class="btn btn-primary btn-sm">
                                    新規求人投稿
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($active_jobs as $job): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h6>
                                        <p class="mb-1 small text-muted">
                                            <?php echo number_format($job['salary_min']); ?>円〜
                                        </p>
                                        <small class="text-muted">
                                            <?php echo date('Y-m-d', strtotime($job['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-success">
                                            <?php echo $job['application_count']; ?>件
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-3">
                                <a href="jobs.php" class="btn btn-outline-success">
                                    すべての求人を見る
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>