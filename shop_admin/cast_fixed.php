<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ファイル読み込み
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

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

$page_title = 'キャスト管理';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// キャスト一覧の取得
$casts = $db->fetchAll("SELECT * FROM casts WHERE shop_id = ?", [$shop_id]);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo htmlspecialchars($shop_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- 店舗管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-store me-2"></i><?php echo htmlspecialchars($shop_name); ?> 管理パネル
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../?page=shop_logout">
                    <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- サイドバー -->
            <div class="col-md-3 col-lg-2">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt me-2"></i>ダッシュボード
                    </a>
                    <a href="shop_info.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-store me-2"></i>店舗情報
                    </a>
                    <a href="jobs.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-briefcase me-2"></i>求人管理
                    </a>
                    <a href="applications.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-alt me-2"></i>応募管理
                    </a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-star me-2"></i>口コミ管理
                    </a>
                    <a href="cast_fixed.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-users me-2"></i>キャスト管理
                    </a>
                </div>
            </div>

            <!-- メインコンテンツ -->
            <div class="col-md-9 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users me-2"></i>キャスト管理
                    </h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCastModal">
                        <i class="fas fa-plus me-2"></i>新しいキャストを追加
                    </button>
                </div>

                <?php if (empty($casts)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">まだキャストが登録されていません</h4>
                            <p class="text-muted">店舗で働くスタッフの情報を登録して、求人に活用しましょう。</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCastModal">
                                <i class="fas fa-plus me-2"></i>最初のキャストを追加
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($casts as $cast): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($cast['name']); ?></h6>
                                        <span class="badge bg-<?php echo $cast['status'] === 'active' ? 'success' : 'warning'; ?>">
                                            <?php echo $cast['status'] === 'active' ? '在籍中' : '非アクティブ'; ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">年齢</small>
                                                <div class="fw-bold"><?php echo $cast['age'] ? $cast['age'] . '歳' : '未設定'; ?></div>
                                            </div>
                                        </div>
                                        <?php if (!empty($cast['special_skill'])): ?>
                                            <div class="mb-3">
                                                <small class="text-muted">特技・専門</small>
                                                <div class="small"><?php echo htmlspecialchars($cast['special_skill']); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($cast['hobby'])): ?>
                                            <p class="card-text text-muted small">
                                                <?php echo htmlspecialchars(mb_substr($cast['hobby'], 0, 80)) . (mb_strlen($cast['hobby']) > 80 ? '...' : ''); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editCastModal<?php echo $cast['id']; ?>">
                                                <i class="fas fa-edit me-1"></i>編集
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteCast(<?php echo $cast['id']; ?>)">
                                                <i class="fas fa-trash me-1"></i>削除
                                            </button>
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

    <!-- 追加モーダル -->
    <div class="modal fade" id="addCastModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">新しいキャストを追加</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">キャスト名 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="age" class="form-label">年齢</label>
                            <input type="number" class="form-control" id="age" name="age" min="16" max="99">
                        </div>
                        
                        <div class="mb-3">
                            <label for="hobby" class="form-label">趣味・特技</label>
                            <textarea class="form-control" id="hobby" name="hobby" rows="3" 
                                      placeholder="キャストの趣味や特技を記載してください"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="special_skill" class="form-label">特技・専門</label>
                            <input type="text" class="form-control" id="special_skill" name="special_skill" 
                                   placeholder="例: 歌、ダンス、接客、料理など">
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">ステータス</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">在籍中</option>
                                <option value="inactive">非アクティブ</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="add_cast" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>追加
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
