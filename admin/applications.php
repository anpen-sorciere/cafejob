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

$page_title = '応募管理';

// ページネーション設定
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// フィルター設定
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// クエリ構築
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "a.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(j.title LIKE ? OR s.name LIKE ? OR u.username LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// 総件数取得
$total_count = $db->fetch(
    "SELECT COUNT(*) as count 
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN shops s ON j.shop_id = s.id
     JOIN users u ON a.user_id = u.id
     $where_clause",
    $params
)['count'];

// 応募一覧取得
$applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, s.name as shop_name, 
            u.username, u.email, u.first_name, u.last_name
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN shops s ON j.shop_id = s.id
     JOIN users u ON a.user_id = u.id
     $where_clause
     ORDER BY a.applied_at DESC
     LIMIT $limit OFFSET $offset",
    $params
);

// ページネーション計算
$total_pages = ceil($total_count / $limit);

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
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">
                    <i class="fas fa-file-alt me-2"></i>応募管理
                </h1>
            </div>
        </div>
        
        <!-- フィルター -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">ステータス</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">すべて</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>承認待ち</option>
                                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>承認済み</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>却下</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search" class="form-label">検索</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="求人タイトル、店舗名、ユーザー名で検索">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>検索
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 応募一覧 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">応募一覧 (<?php echo number_format($total_count); ?>件)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($applications)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">応募が見つかりませんでした</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>求人</th>
                                            <th>店舗</th>
                                            <th>応募者</th>
                                            <th>ステータス</th>
                                            <th>応募日</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $application): ?>
                                            <tr>
                                                <td><?php echo $application['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($application['job_title']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($application['shop_name']); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($application['username']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($application['last_name'] . ' ' . $application['first_name']); ?></small>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($application['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $application['status'] === 'approved' ? 'success' : ($application['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo $application['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('Y/m/d H:i', strtotime($application['applied_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-success" onclick="approveApplication(<?php echo $application['id']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" onclick="rejectApplication(<?php echo $application['id']; ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info" onclick="viewApplication(<?php echo $application['id']; ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- ページネーション -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="ページネーション">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">前へ</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">次へ</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function approveApplication(id) {
        if (confirm('この応募を承認しますか？')) {
            // 承認処理を実装
            alert('承認機能は実装中です');
        }
    }
    
    function rejectApplication(id) {
        if (confirm('この応募を却下しますか？')) {
            // 却下処理を実装
            alert('却下機能は実装中です');
        }
    }
    
    function viewApplication(id) {
        // 詳細表示処理を実装
        alert('詳細表示機能は実装中です');
    }
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
