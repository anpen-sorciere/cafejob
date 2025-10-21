<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes.php';

// 管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

$page_title = '管理者ダッシュボード';
$admin_role = $_SESSION['admin_role'];

// 統計データの取得
$stats = [
    'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'],
    'total_shops' => $db->fetch("SELECT COUNT(*) as count FROM shops")['count'],
    'total_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs")['count'],
    'total_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications")['count'],
    'total_casts' => $db->fetch("SELECT COUNT(*) as count FROM casts")['count'],
    'pending_shops' => $db->fetch("SELECT COUNT(*) as count FROM shops WHERE status IN ('pending', 'verification_pending')")['count'],
    'pending_reviews' => $db->fetch("SELECT COUNT(*) as count FROM reviews WHERE status = 'pending'")['count']
];

// 最新の応募情報
try {
    $recent_applications = $db->fetchAll(
        "SELECT a.*, j.title as job_title, s.name as shop_name, u.username, u.email
         FROM applications a
         JOIN jobs j ON a.job_id = j.id
         JOIN shops s ON j.shop_id = s.id
         JOIN users u ON a.user_id = u.id
         ORDER BY a.applied_at DESC
         LIMIT 10"
    );
} catch (Exception $e) {
    $recent_applications = [];
}

// 最新の店舗登録
try {
    $recent_shops = $db->fetchAll(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name
         FROM shops s
         LEFT JOIN prefectures p ON s.prefecture_id = p.id
         LEFT JOIN cities c ON s.city_id = c.id
         ORDER BY s.created_at DESC
         LIMIT 10"
    );
} catch (Exception $e) {
    $recent_shops = [];
}

// 最新の口コミ
try {
    $recent_reviews = $db->fetchAll(
        "SELECT r.*, s.name as shop_name, u.username
         FROM reviews r
         JOIN shops s ON r.shop_id = s.id
         LEFT JOIN users u ON r.user_id = u.id
         ORDER BY r.created_at DESC
         LIMIT 10"
    );
} catch (Exception $e) {
    $recent_reviews = [];
}

// time_ago関数はincludes/functions.phpで定義済み

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
    <!-- 管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>管理者パネル
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i>ダッシュボード
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shops.php">
                            <i class="fas fa-store me-1"></i>店舗管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">
                            <i class="fas fa-briefcase me-1"></i>求人管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-1"></i>ユーザー管理
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
                        <a class="nav-link" href="sample_data.php">
                            <i class="fas fa-database me-1"></i>サンプルデータ
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
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
        
        <!-- URL情報カード -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-link me-2"></i>サイトURL情報
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-users fa-2x text-primary me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">ユーザートップページ</h6>
                                        <p class="mb-0 text-muted">一般ユーザー向けメインページ</p>
                                        <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i>サイトを表示
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-store fa-2x text-success me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">店舗ログインページ</h6>
                                        <p class="mb-0 text-muted">店舗管理者向けログインページ</p>
                                        <a href="<?php echo SITE_URL; ?>?page=shop_login" target="_blank" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i>店舗ログイン
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-light mb-0">
                                    <small class="text-muted">
                                        <strong>ユーザートップページ:</strong> <?php echo SITE_URL; ?><br>
                                        <strong>店舗ログインページ:</strong> <?php echo SITE_URL; ?>?page=shop_login<br>
                                        <strong>システム管理者ログイン:</strong> <?php echo SITE_URL; ?>?page=admin_login
                                    </small>
                                </div>
                            </div>
                        </div>
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
                                    総ユーザー数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total_users']); ?>
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
                                    <?php echo number_format($stats['total_shops']); ?>
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
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
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
        </div>
        
        <!-- 追加統計カード -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    総キャスト数
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total_casts']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 承認待ちアイテム -->
        <?php if ($stats['pending_shops'] > 0 || $stats['pending_reviews'] > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>承認待ちアイテム
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($stats['pending_shops'] > 0): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-store fa-2x text-warning me-3"></i>
                                    <div>
                                        <h6 class="mb-1">店舗承認待ち</h6>
                                        <p class="mb-0 text-muted"><?php echo $stats['pending_shops']; ?>件の店舗が承認待ちです</p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="shops.php?status=pending" class="btn btn-warning btn-sm">
                                            確認する
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($stats['pending_reviews'] > 0): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-star fa-2x text-warning me-3"></i>
                                    <div>
                                        <h6 class="mb-1">口コミ承認待ち</h6>
                                        <p class="mb-0 text-muted"><?php echo $stats['pending_reviews']; ?>件の口コミが承認待ちです</p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="reviews.php?status=pending" class="btn btn-warning btn-sm">
                                            確認する
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 最新情報 -->
        <div class="row">
            <!-- 最新の応募 -->
            <div class="col-lg-4 mb-4">
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
                                            <?php echo htmlspecialchars($application['shop_name']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($application['username']); ?> | 
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
            
            <!-- 最新の店舗 -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-store me-2"></i>最新の店舗
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_shops)): ?>
                            <p class="text-muted text-center">店舗がありません</p>
                        <?php else: ?>
                            <?php foreach ($recent_shops as $shop): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($shop['name']); ?></h6>
                                        <p class="mb-1 small text-muted">
                                            <?php echo htmlspecialchars($shop['prefecture_name'] . $shop['city_name']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo time_ago($shop['created_at']); ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php echo $shop['status'] == 'active' ? 'success' : ($shop['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo $shop['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- 最新の口コミ -->
            <div class="col-lg-4 mb-4">
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
                                        <h6 class="mb-1"><?php echo htmlspecialchars($review['shop_name']); ?></h6>
                                        <p class="mb-1 small text-muted">
                                            <?php echo htmlspecialchars($review['title']); ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
ob_end_flush();
?>
