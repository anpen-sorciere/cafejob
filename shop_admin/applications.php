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

$page_title = '応募管理';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// フィルタリング用のパラメータ
$status_filter = $_GET['status'] ?? 'all';
$job_filter = $_GET['job_id'] ?? 'all';
$search = $_GET['search'] ?? '';

// 応募一覧の取得（フィルタリング対応）
$where_conditions = ["j.shop_id = ?"];
$params = [$shop_id];

if ($status_filter !== 'all') {
    $where_conditions[] = "a.status = ?";
    $params[] = $status_filter;
}

if ($job_filter !== 'all') {
    $where_conditions[] = "a.job_id = ?";
    $params[] = $job_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(u.username LIKE ? OR u.email LIKE ? OR j.title LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(' AND ', $where_conditions);

$applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, j.id as job_id,
            u.username, u.email, u.phone, u.first_name, u.last_name,
            u.created_at as user_created_at
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN users u ON a.user_id = u.id
     WHERE {$where_clause}
     ORDER BY a.applied_at DESC",
    $params
);

// 店舗の求人一覧（フィルター用）
$shop_jobs = $db->fetchAll(
    "SELECT id, title FROM jobs WHERE shop_id = ? ORDER BY title",
    [$shop_id]
);

// 応募ステータス更新処理
if ($_POST && isset($_POST['update_status'])) {
    $application_id = (int)$_POST['application_id'];
    $new_status = sanitize_input($_POST['status']);
    $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');
    
    // 自分の店舗の応募かチェック
    $application = $db->fetch(
        "SELECT a.id FROM applications a 
         JOIN jobs j ON a.job_id = j.id 
         WHERE a.id = ? AND j.shop_id = ?",
        [$application_id, $shop_id]
    );
    
    if ($application && in_array($new_status, ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])) {
        $db->query(
            "UPDATE applications SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?",
            [$new_status, $admin_notes, $application_id]
        );
        $_SESSION['success_message'] = '応募ステータスを更新しました。';
        header('Location: applications.php?' . http_build_query($_GET));
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
                        <a class="nav-link" href="jobs.php">
                            <i class="fas fa-briefcase me-1"></i>求人管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="applications.php">
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
                        <i class="fas fa-file-alt me-2"></i>応募管理
                    </h1>
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        総応募数: <span class="fw-bold"><?php echo count($applications); ?></span>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- フィルター -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">ステータス</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>すべて</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>未読</option>
                                    <option value="reviewed" <?php echo $status_filter === 'reviewed' ? 'selected' : ''; ?>>読了</option>
                                    <option value="interview" <?php echo $status_filter === 'interview' ? 'selected' : ''; ?>>面接</option>
                                    <option value="accepted" <?php echo $status_filter === 'accepted' ? 'selected' : ''; ?>>採用</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>不採用</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="job_id" class="form-label">求人</label>
                                <select class="form-select" id="job_id" name="job_id">
                                    <option value="all" <?php echo $job_filter === 'all' ? 'selected' : ''; ?>>すべての求人</option>
                                    <?php foreach ($shop_jobs as $job): ?>
                                        <option value="<?php echo $job['id']; ?>" <?php echo $job_filter == $job['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">検索</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="応募者名、メールアドレス、求人タイトルで検索"
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>検索
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (empty($applications)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">応募がありません</h4>
                            <p class="text-muted">求人を投稿すると、応募がここに表示されます。</p>
                            <a href="jobs.php" class="btn btn-primary">
                                <i class="fas fa-briefcase me-2"></i>求人管理に戻る
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- もえなび！スタイルの応募一覧 -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>応募一覧
                                <span class="badge bg-light text-primary ms-2"><?php echo count($applications); ?>件</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($applications as $index => $application): ?>
                                <div class="border-bottom p-3 application-item" data-application-id="<?php echo $application['id']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="me-3">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">
                                                        <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?>
                                                        <span class="badge bg-<?php 
                                                            echo $application['status'] === 'pending' ? 'warning' : 
                                                                ($application['status'] === 'reviewed' ? 'info' : 
                                                                    ($application['status'] === 'interview' ? 'primary' : 
                                                                        ($application['status'] === 'accepted' ? 'success' : 'danger'))); 
                                                        ?> ms-2">
                                                            <?php 
                                                            $status_labels = [
                                                                'pending' => '未読',
                                                                'reviewed' => '読了',
                                                                'interview' => '面接',
                                                                'accepted' => '採用',
                                                                'rejected' => '不採用'
                                                            ];
                                                            echo $status_labels[$application['status']] ?? $application['status'];
                                                            ?>
                                                        </span>
                                                    </h6>
                                                    <p class="mb-1 text-muted small">
                                                        <i class="fas fa-briefcase me-1"></i>
                                                        <?php echo htmlspecialchars($application['job_title']); ?>
                                                    </p>
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        <?php echo htmlspecialchars($application['email']); ?>
                                                        <?php if (!empty($application['phone'])): ?>
                                                            <span class="ms-3">
                                                                <i class="fas fa-phone me-1"></i>
                                                                <?php echo htmlspecialchars($application['phone']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if (!empty($application['message'])): ?>
                                                        <div class="mt-2">
                                                            <p class="mb-0 small text-truncate" style="max-width: 500px;">
                                                                <i class="fas fa-comment me-1"></i>
                                                                <?php echo htmlspecialchars($application['message']); ?>
                                                            </p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('Y年m月d日 H:i', strtotime($application['applied_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $application['id']; ?>">
                                                    <i class="fas fa-edit me-1"></i>ステータス更新
                                                </button>
                                                <a href="application_detail.php?id=<?php echo $application['id']; ?>" 
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye me-1"></i>詳細
                                                </a>
                                                <a href="chat_detail.php?application_id=<?php echo $application['id']; ?>" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-comments me-1"></i>チャット
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- ステータス更新モーダル -->
                            <div class="modal fade" id="statusModal<?php echo $application['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">応募ステータス更新</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label for="status<?php echo $application['id']; ?>" class="form-label">ステータス</label>
                                                    <select class="form-select" id="status<?php echo $application['id']; ?>" name="status" required>
                                                        <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>未読</option>
                                                        <option value="reviewed" <?php echo $application['status'] === 'reviewed' ? 'selected' : ''; ?>>読了</option>
                                                        <option value="interview" <?php echo $application['status'] === 'interview' ? 'selected' : ''; ?>>面接</option>
                                                        <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>採用</option>
                                                        <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>不採用</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="admin_notes<?php echo $application['id']; ?>" class="form-label">管理者メモ</label>
                                                    <textarea class="form-control" id="admin_notes<?php echo $application['id']; ?>" 
                                                              name="admin_notes" rows="3" 
                                                              placeholder="面接日時、採用理由、不採用理由などを記録してください"><?php echo htmlspecialchars($application['admin_notes'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                                <button type="submit" name="update_status" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>更新
                                                </button>
                                            </div>
                                        </form>
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
    
    <style>
    /* もえなび！スタイルの応募管理画面 */
    .application-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .application-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .application-item:last-child {
        border-bottom: none !important;
    }
    
    .application-item .bg-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
    }
    
    .card-header.bg-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.5em 0.75em;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* レスポンシブ対応 */
    @media (max-width: 768px) {
        .application-item .row {
            flex-direction: column;
        }
        
        .application-item .col-md-4 {
            text-align: left !important;
            margin-top: 1rem;
        }
        
        .application-item .d-flex.gap-2 {
            flex-wrap: wrap;
        }
        
        .application-item .btn-sm {
            margin-bottom: 0.5rem;
        }
    }
    
    /* アニメーション効果 */
    .application-item {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* ステータス別の色分け */
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge.bg-info {
        background-color: #17a2b8 !important;
    }
    
    .badge.bg-primary {
        background-color: #007bff !important;
    }
    
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    </style>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
