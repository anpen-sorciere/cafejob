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

$page_title = '求人管理';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// 求人一覧の取得
$jobs = $db->fetchAll(
    "SELECT j.*, 
            COUNT(a.id) as application_count,
            COUNT(CASE WHEN a.status = 'pending' THEN 1 END) as pending_applications
     FROM jobs j
     LEFT JOIN applications a ON j.id = a.job_id
     WHERE j.shop_id = ?
     GROUP BY j.id
     ORDER BY j.created_at DESC",
    [$shop_id]
);

// 求人制限の計算
$total_jobs = count($jobs);
$basic_job_limit = 1; // 基本料金に含まれる求人数
$can_add_more = $total_jobs < $basic_job_limit;
$additional_jobs = max(0, $total_jobs - $basic_job_limit);

// 求人ステータス更新処理
if ($_POST && isset($_POST['update_status'])) {
    $job_id = (int)$_POST['job_id'];
    $new_status = sanitize_input($_POST['status']);
    
    // 自分の店舗の求人かチェック
    $job = $db->fetch("SELECT id FROM jobs WHERE id = ? AND shop_id = ?", [$job_id, $shop_id]);
    
    if ($job && in_array($new_status, ['active', 'inactive', 'closed'])) {
        $db->query("UPDATE jobs SET status = ?, updated_at = NOW() WHERE id = ?", [$new_status, $job_id]);
        $_SESSION['success_message'] = '求人ステータスを更新しました。';
        header('Location: jobs.php');
        exit;
    }
}

// 求人削除処理
if ($_POST && isset($_POST['delete_job'])) {
    $job_id = (int)$_POST['job_id'];
    
    // 自分の店舗の求人かチェック
    $job = $db->fetch("SELECT id FROM jobs WHERE id = ? AND shop_id = ?", [$job_id, $shop_id]);
    
    if ($job) {
        // 関連する応募データも削除
        $db->query("DELETE FROM applications WHERE job_id = ?", [$job_id]);
        $db->query("DELETE FROM jobs WHERE id = ?", [$job_id]);
        $_SESSION['success_message'] = '求人を削除しました。';
        header('Location: jobs.php');
        exit;
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
                        <a class="nav-link" href="shop_info.php">
                            <i class="fas fa-store me-1"></i>店舗情報
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="jobs.php">
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
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-briefcase me-2"></i>求人管理
                    </h1>
                    <div class="d-flex align-items-center gap-3">
                        <!-- 求人制限表示 -->
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            基本求人: <span class="fw-bold"><?php echo $total_jobs; ?>/<?php echo $basic_job_limit; ?></span>
                            <?php if ($additional_jobs > 0): ?>
                                <span class="text-warning">(+<?php echo $additional_jobs; ?> オプション)</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($can_add_more): ?>
                            <a href="job_create.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>新しい求人を投稿
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-primary" disabled>
                                <i class="fas fa-plus me-2"></i>新しい求人を投稿
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- 求人制限情報 -->
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>求人制限について
                    </h6>
                    <p class="mb-2">
                        <strong>基本料金</strong>：1つの求人まで無料で投稿できます。<br>
                        <strong>追加求人</strong>：2つ目以降はオプション課金が必要です。
                    </p>
                    <?php if ($total_jobs >= $basic_job_limit): ?>
                        <p class="mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            基本求人制限に達しています。追加求人をご希望の場合は、オプション課金をお申し込みください。
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (empty($jobs)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">まだ求人が投稿されていません</h4>
                            <p class="text-muted">新しい求人を投稿して、優秀なスタッフを見つけましょう。</p>
                            <a href="job_create.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>最初の求人を投稿
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($jobs as $loop_index => $job): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($job['title']); ?></h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-<?php echo $job['status'] === 'active' ? 'success' : ($job['status'] === 'inactive' ? 'warning' : 'secondary'); ?>">
                                                <?php echo $job['status'] === 'active' ? '公開中' : ($job['status'] === 'inactive' ? '非公開' : '終了'); ?>
                                            </span>
                                            <?php if ($loop_index >= $basic_job_limit): ?>
                                                <span class="badge bg-warning">オプション</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars(mb_substr($job['description'], 0, 100)) . (mb_strlen($job['description']) > 100 ? '...' : ''); ?>
                                        </p>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted">応募数</small>
                                                <div class="fw-bold"><?php echo $job['application_count']; ?></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">未読</small>
                                                <div class="fw-bold text-warning"><?php echo $job['pending_applications']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex gap-2">
                                            <a href="job_edit.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i>編集
                                            </a>
                                            <a href="applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-file-alt me-1"></i>応募
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-cog me-1"></i>操作
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                                            <input type="hidden" name="status" value="<?php echo $job['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                                            <button type="submit" name="update_status" class="dropdown-item">
                                                                <i class="fas fa-eye<?php echo $job['status'] === 'active' ? '-slash' : ''; ?> me-2"></i>
                                                                <?php echo $job['status'] === 'active' ? '非公開にする' : '公開する'; ?>
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('この求人を削除しますか？関連する応募データも削除されます。')">
                                                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                                            <button type="submit" name="delete_job" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>削除
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
