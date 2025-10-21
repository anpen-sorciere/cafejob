<?php
$page_title = '店舗ダッシュボード';
$page_description = '店舗管理者のダッシュボードです。';

// 店舗管理者認証チェック
if (!isset($_SESSION['shop_admin_id'])) {
    header('Location: ?page=shop_login');
    exit;
}

$shop_id = $_SESSION['shop_id'];

// 統計データの取得
$stats = [
    'total_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE shop_id = ?", [$shop_id])['count'],
    'active_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE shop_id = ? AND status = 'active'", [$shop_id])['count'],
    'total_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.shop_id = ?", [$shop_id])['count'],
    'pending_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.shop_id = ? AND a.status = 'pending'", [$shop_id])['count']
];

// 最新の応募情報
$recent_applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, u.username, u.email, u.phone
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN users u ON a.user_id = u.id
     WHERE j.shop_id = ?
     ORDER BY a.applied_at DESC
     LIMIT 10",
    [$shop_id]
);

// 店舗の求人一覧
$shop_jobs = $db->fetchAll(
    "SELECT * FROM jobs WHERE shop_id = ? ORDER BY created_at DESC",
    [$shop_id]
);

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-store me-2"></i><?php echo htmlspecialchars($_SESSION['shop_name']); ?> ダッシュボード
                </h1>
                <div>
                    <a href="?page=shop_logout" class="btn btn-outline-danger">
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
                            <a href="?page=job_post" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-1"></i>新規求人投稿
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?page=shop_jobs" class="btn btn-outline-primary w-100">
                                <i class="fas fa-briefcase me-1"></i>求人管理
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?page=shop_applications" class="btn btn-outline-success w-100">
                                <i class="fas fa-file-alt me-1"></i>応募管理
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?page=shop_profile" class="btn btn-outline-info w-100">
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
                                        <?php echo time_ago($application['applied_at']); ?>
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
                            <a href="?page=shop_applications" class="btn btn-outline-primary">
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
                    <?php if (empty($shop_jobs)): ?>
                        <p class="text-muted text-center">求人がありません</p>
                        <div class="text-center">
                            <a href="?page=job_post" class="btn btn-primary btn-sm">
                                新規求人投稿
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($shop_jobs as $job): ?>
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
                                        <?php echo time_ago($job['created_at']); ?>
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-<?php echo $job['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo $job['status']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center mt-3">
                            <a href="?page=shop_jobs" class="btn btn-outline-success">
                                すべての求人を見る
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
