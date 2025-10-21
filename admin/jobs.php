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

$page_title = '求人管理';

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
    $where_conditions[] = "j.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(j.title LIKE ? OR s.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// 総件数取得
$total_count = $db->fetch(
    "SELECT COUNT(*) as count FROM jobs j JOIN shops s ON j.shop_id = s.id $where_clause",
    $params
)['count'];

// 求人一覧取得
$jobs = $db->fetchAll(
    "SELECT j.*, s.name as shop_name, s.address as shop_address,
            COUNT(a.id) as application_count
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN applications a ON j.id = a.job_id
     $where_clause
     GROUP BY j.id
     ORDER BY j.id DESC
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
                    <i class="fas fa-briefcase me-2"></i>求人管理
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
                                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>アクティブ</option>
                                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>非アクティブ</option>
                                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>募集終了</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search" class="form-label">検索</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="求人タイトルまたは店舗名で検索">
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
        
        <!-- 求人一覧 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">求人一覧 (<?php echo number_format($total_count); ?>件)</h5>
                        <a href="job_add.php" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>新規求人追加
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($jobs)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <p class="text-muted">求人が見つかりませんでした</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>求人タイトル</th>
                                            <th>店舗名</th>
                                            <th>給与</th>
                                            <th>応募数</th>
                                            <th>ステータス</th>
                                            <th>募集締切</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $job): ?>
                                            <tr>
                                                <td><?php echo $job['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($job['job_type']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($job['shop_name']); ?></td>
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
                                                    <span class="badge bg-<?php echo $job['status'] === 'active' ? 'success' : ($job['status'] === 'closed' ? 'danger' : 'secondary'); ?>">
                                                        <?php echo $job['status']; ?>
                                                    </span>
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
                                                        <a href="job_view.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteJob(<?php echo $job['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
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
    function deleteJob(id) {
        if (confirm('この求人を削除しますか？この操作は取り消せません。')) {
            // 削除処理を実装
            alert('削除機能は実装中です');
        }
    }
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
