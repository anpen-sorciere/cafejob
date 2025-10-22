<?php
/**
 * 確認コード入力履歴管理
 * 運営側で入力ミス履歴を確認できる
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// 管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

$db = new Database();

// ページネーション
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// フィルター
$shop_id_filter = isset($_GET['shop_id']) ? (int)$_GET['shop_id'] : '';
$attempt_type_filter = isset($_GET['attempt_type']) ? $_GET['attempt_type'] : '';
$is_successful_filter = isset($_GET['is_successful']) ? $_GET['is_successful'] : '';

// クエリ条件の構築
$where_conditions = [];
$params = [];

if ($shop_id_filter) {
    $where_conditions[] = "va.shop_id = ?";
    $params[] = $shop_id_filter;
}

if ($attempt_type_filter) {
    $where_conditions[] = "va.attempt_type = ?";
    $params[] = $attempt_type_filter;
}

if ($is_successful_filter !== '') {
    $where_conditions[] = "va.is_successful = ?";
    $params[] = $is_successful_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// 入力履歴の取得
$attempts = $db->fetchAll(
    "SELECT va.*, s.name as shop_name, s.email as shop_email
     FROM verification_attempts va
     JOIN shops s ON va.shop_id = s.id
     {$where_clause}
     ORDER BY va.attempt_time DESC
     LIMIT {$limit} OFFSET {$offset}",
    $params
);

// 総件数の取得
$total_count = $db->fetch(
    "SELECT COUNT(*) as count
     FROM verification_attempts va
     JOIN shops s ON va.shop_id = s.id
     {$where_clause}",
    $params
)['count'];

$total_pages = ceil($total_count / $limit);

// 店舗一覧（フィルター用）
$shops = $db->fetchAll("SELECT id, name FROM shops ORDER BY name");

$page_title = '確認コード入力履歴';
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
                <i class="fas fa-cog me-2"></i>管理者パネル
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-tachometer-alt me-1"></i>ダッシュボード
                </a>
                <a class="nav-link active" href="verification_attempts.php">
                    <i class="fas fa-key me-1"></i>確認コード履歴
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-key me-2"></i>確認コード入力履歴
                    </h1>
                    <div class="text-muted small">
                        総件数: <?php echo number_format($total_count); ?>件
                    </div>
                </div>

                <!-- フィルター -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="shop_id" class="form-label">店舗</label>
                                <select class="form-select" id="shop_id" name="shop_id">
                                    <option value="">すべて</option>
                                    <?php foreach ($shops as $shop): ?>
                                        <option value="<?php echo $shop['id']; ?>" 
                                                <?php echo $shop_id_filter == $shop['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($shop['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="attempt_type" class="form-label">タイプ</label>
                                <select class="form-select" id="attempt_type" name="attempt_type">
                                    <option value="">すべて</option>
                                    <option value="initial_registration" <?php echo $attempt_type_filter === 'initial_registration' ? 'selected' : ''; ?>>初期登録</option>
                                    <option value="address_change" <?php echo $attempt_type_filter === 'address_change' ? 'selected' : ''; ?>>住所変更</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="is_successful" class="form-label">結果</label>
                                <select class="form-select" id="is_successful" name="is_successful">
                                    <option value="">すべて</option>
                                    <option value="1" <?php echo $is_successful_filter === '1' ? 'selected' : ''; ?>>成功</option>
                                    <option value="0" <?php echo $is_successful_filter === '0' ? 'selected' : ''; ?>>失敗</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>検索
                                </button>
                                <a href="verification_attempts.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>クリア
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 履歴一覧 -->
                <div class="card">
                    <div class="card-body p-0">
                        <?php if (empty($attempts)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-key fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">入力履歴がありません</h5>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>日時</th>
                                            <th>店舗</th>
                                            <th>タイプ</th>
                                            <th>正しいコード</th>
                                            <th>入力コード</th>
                                            <th>結果</th>
                                            <th>IPアドレス</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attempts as $attempt): ?>
                                            <tr>
                                                <td>
                                                    <small><?php echo date('Y/m/d H:i:s', strtotime($attempt['attempt_time'])); ?></small>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($attempt['shop_name']); ?></strong>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($attempt['shop_email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $attempt['attempt_type'] === 'initial_registration' ? 'primary' : 'info'; ?>">
                                                        <?php echo $attempt['attempt_type'] === 'initial_registration' ? '初期登録' : '住所変更'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($attempt['verification_code']); ?></code>
                                                </td>
                                                <td>
                                                    <code class="<?php echo $attempt['is_successful'] ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo htmlspecialchars($attempt['input_code']); ?>
                                                    </code>
                                                </td>
                                                <td>
                                                    <?php if ($attempt['is_successful']): ?>
                                                        <span class="badge bg-success">成功</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">失敗</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($attempt['ip_address']); ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ページネーション -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="ページネーション" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query($_GET); ?>">前へ</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query($_GET); ?>">次へ</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
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
