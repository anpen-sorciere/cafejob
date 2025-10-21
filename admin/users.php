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

$page_title = 'ユーザー管理';

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
    $where_conditions[] = "u.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(u.username LIKE ? OR u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// 総件数取得
$total_count = $db->fetch(
    "SELECT COUNT(*) as count FROM users u $where_clause",
    $params
)['count'];

// ユーザー一覧取得
$users = $db->fetchAll(
    "SELECT u.*, COUNT(a.id) as application_count
     FROM users u
     LEFT JOIN applications a ON u.id = a.user_id
     $where_clause
     GROUP BY u.id
     ORDER BY u.id DESC
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
                    <i class="fas fa-users me-2"></i>ユーザー管理
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
                                    <option value="banned" <?php echo $status_filter === 'banned' ? 'selected' : ''; ?>>BAN</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search" class="form-label">検索</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="ユーザー名、メール、氏名で検索">
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
        
        <!-- ユーザー一覧 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ユーザー一覧 (<?php echo number_format($total_count); ?>件)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ユーザーが見つかりませんでした</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ユーザー名</th>
                                            <th>氏名</th>
                                            <th>メールアドレス</th>
                                            <th>電話番号</th>
                                            <th>応募数</th>
                                            <th>ステータス</th>
                                            <th>登録日</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($user['last_name'] . ' ' . $user['first_name']); ?>
                                                    <?php if ($user['gender']): ?>
                                                        <br><small class="text-muted"><?php echo $user['gender'] === 'male' ? '男性' : ($user['gender'] === 'female' ? '女性' : 'その他'); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['phone'] ?: '-'); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $user['application_count']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : ($user['status'] === 'banned' ? 'danger' : 'secondary'); ?>">
                                                        <?php echo $user['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('Y/m/d', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="user_view.php?id=<?php echo $user['id']; ?>" class="btn btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-warning" onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['status']; ?>')">
                                                            <i class="fas fa-user-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?>"></i>
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
    function toggleUserStatus(id, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'banned' : 'active';
        const action = newStatus === 'banned' ? 'BAN' : 'アクティブ化';
        
        if (confirm(`このユーザーを${action}しますか？`)) {
            // ステータス変更処理を実装
            alert('ステータス変更機能は実装中です');
        }
    }
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
