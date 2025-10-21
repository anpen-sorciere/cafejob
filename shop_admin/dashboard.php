<?php
session_start();
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

$page_title = '店舗管理ダッシュボード';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// 統計データの取得
$stats = [
    'total_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE shop_id = ?", [$shop_id])['count'],
    'active_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE shop_id = ? AND status = 'active'", [$shop_id])['count'],
    'total_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.shop_id = ?", [$shop_id])['count'],
    'pending_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.shop_id = ? AND a.status = 'pending'", [$shop_id])['count'],
    'total_reviews' => $db->fetch("SELECT COUNT(*) as count FROM reviews WHERE shop_id = ?", [$shop_id])['count'],
    'average_rating' => $db->fetch("SELECT AVG(rating) as avg FROM reviews WHERE shop_id = ? AND status = 'approved'", [$shop_id])['avg'] ?: 0
];

// 最新の応募情報
$recent_applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, u.username, u.email, u.first_name, u.last_name
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN users u ON a.user_id = u.id
     WHERE j.shop_id = ?
     ORDER BY a.applied_at DESC
     LIMIT 5",
    [$shop_id]
);

// 最新の口コミ
$recent_reviews = $db->fetchAll(
    "SELECT r.*, u.username, u.first_name, u.last_name
     FROM reviews r
     LEFT JOIN users u ON r.user_id = u.id
     WHERE r.shop_id = ?
     ORDER BY r.created_at DESC
     LIMIT 5",
    [$shop_id]
);

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

ob_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- 店舗管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>ダッシュボード
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop_info.php">
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
                    <li class="nav-item">
                        <a class="nav-link" href="reviews.php">
                            <i class="fas fa-star me-1"></i>口コミ管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cast_management.php">
                            <i class="fas fa-users me-1"></i>キャスト管理
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars($_SESSION['shop_admin_username'] ?? '管理者'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>サイトを表示
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                            </a></li>
                        </ul>
                    </li>
                </ul>
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
                                    アクティブ求人
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['active_jobs']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                    承認待ち応募
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
        
        <!-- 承認待ち応募の通知 -->
        <?php if ($stats['pending_applications'] > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>承認待ち応募
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt fa-2x text-warning me-3"></i>
                            <div>
                                <h6 class="mb-1"><?php echo $stats['pending_applications']; ?>件の応募が承認待ちです</h6>
                                <p class="mb-0 text-muted">応募管理ページで確認・承認してください</p>
                            </div>
                            <div class="ms-auto">
                                <a href="applications.php?status=pending" class="btn btn-warning">
                                    確認する
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
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
                            <div class="col-md-4 mb-2">
                                <a href="job_create.php" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-1"></i>新規求人投稿
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="jobs.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-briefcase me-1"></i>求人管理
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="applications.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-file-alt me-1"></i>応募管理
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 最新情報 -->
        <div class="row">
            <!-- 最新の応募 -->
            <div class="col-lg-6 mb-4">
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
                                            <?php echo htmlspecialchars($application['last_name'] . ' ' . $application['first_name']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($application['email']); ?> | 
                                            <?php echo time_ago($application['applied_at']); ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php echo $application['status'] == 'pending' ? 'warning' : ($application['status'] == 'accepted' ? 'success' : 'danger'); ?>">
                                            <?php echo $application['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- 最新の口コミ -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-star me-2"></i>最新の口コミ
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_reviews)): ?>
                            <p class="text-muted text-center">口コミがありません</p>
                        <?php else: ?>
                            <?php foreach ($recent_reviews as $review): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mb-1 small text-muted">
                                            <?php echo htmlspecialchars(mb_substr($review['content'], 0, 50)); ?>
                                            <?php if (mb_strlen($review['content']) > 50): ?>
                                                <span class="text-muted">...</span>
                                            <?php endif; ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($review['username'] ?: '匿名'); ?> | 
                                            <?php echo time_ago($review['created_at']); ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php echo $review['status'] == 'approved' ? 'success' : ($review['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo $review['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- アクティブな求人 -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-briefcase me-2"></i>アクティブな求人
                        </h6>
                        <a href="jobs.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i>新規求人
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($active_jobs)): ?>
                            <p class="text-muted text-center">アクティブな求人がありません</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>求人タイトル</th>
                                            <th>給与</th>
                                            <th>応募数</th>
                                            <th>募集締切</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($active_jobs as $job): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($job['job_type']); ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                                        <?php echo number_format($job['salary_min']); ?>円 - <?php echo number_format($job['salary_max']); ?>円
                                                    <?php elseif ($job['salary_min']): ?>
                                                        <?php echo number_format($job['salary_min']); ?>円以上
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $job['application_count']; ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($job['application_deadline']): ?>
                                                        <?php echo date('Y/m/d', strtotime($job['application_deadline'])); ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="job_edit.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
