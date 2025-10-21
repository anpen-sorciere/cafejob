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
$casts = $db->fetchAll(
    "SELECT c.*, 
            COUNT(j.id) as job_count
     FROM casts c
     LEFT JOIN jobs j ON c.id = j.cast_id
     WHERE c.shop_id = ?
     GROUP BY c.id
     ORDER BY c.created_at DESC",
    [$shop_id]
);

// キャスト登録処理
if ($_POST && isset($_POST['add_cast'])) {
    $name = sanitize_input($_POST['name']);
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $hobby = sanitize_input($_POST['hobby']);
    $special_skill = sanitize_input($_POST['special_skill']);
    $status = sanitize_input($_POST['status']);
    
    $errors = [];
    
    // バリデーション
    if (empty($name)) $errors[] = 'キャスト名を入力してください';
    
    if (empty($errors)) {
        try {
            $db->query(
                "INSERT INTO casts (shop_id, name, age, hobby, special_skill, status, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())",
                [$shop_id, $name, $age, $hobby, $special_skill, $status]
            );
            
            $_SESSION['success_message'] = 'キャストを追加しました。';
            header('Location: cast_management.php');
            exit;
            
        } catch (Exception $e) {
            $errors[] = 'キャスト追加中にエラーが発生しました: ' . $e->getMessage();
        }
    }
}

// キャスト更新処理
if ($_POST && isset($_POST['update_cast'])) {
    $cast_id = (int)$_POST['cast_id'];
    $name = sanitize_input($_POST['name']);
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $hobby = sanitize_input($_POST['hobby']);
    $special_skill = sanitize_input($_POST['special_skill']);
    $status = sanitize_input($_POST['status']);
    
    // 自分の店舗のキャストかチェック
    $cast = $db->fetch("SELECT id FROM casts WHERE id = ? AND shop_id = ?", [$cast_id, $shop_id]);
    
    if ($cast) {
        try {
            $db->query(
                "UPDATE casts SET name = ?, age = ?, hobby = ?, special_skill = ?, status = ?, updated_at = NOW() 
                 WHERE id = ? AND shop_id = ?",
                [$name, $age, $hobby, $special_skill, $status, $cast_id, $shop_id]
            );
            
            $_SESSION['success_message'] = 'キャスト情報を更新しました。';
            header('Location: cast_management.php');
            exit;
            
        } catch (Exception $e) {
            $errors[] = 'キャスト更新中にエラーが発生しました: ' . $e->getMessage();
        }
    }
}

// キャスト削除処理
if ($_POST && isset($_POST['delete_cast'])) {
    $cast_id = (int)$_POST['cast_id'];
    
    // 自分の店舗のキャストかチェック
    $cast = $db->fetch("SELECT id FROM casts WHERE id = ? AND shop_id = ?", [$cast_id, $shop_id]);
    
    if ($cast) {
        $db->query("DELETE FROM casts WHERE id = ?", [$cast_id]);
        $_SESSION['success_message'] = 'キャストを削除しました。';
        header('Location: cast_management.php');
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
                        <a class="nav-link" href="applications.php">
                            <i class="fas fa-file-alt me-1"></i>応募管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cast_management.php">
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
                        <i class="fas fa-users me-2"></i>キャスト管理
                    </h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCastModal">
                        <i class="fas fa-plus me-2"></i>新しいキャストを追加
                    </button>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

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
                                            <div class="col-6">
                                                <small class="text-muted">担当求人</small>
                                                <div class="fw-bold"><?php echo $cast['job_count']; ?>件</div>
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
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#editCastModal<?php echo $cast['id']; ?>">
                                                <i class="fas fa-edit me-1"></i>編集
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-cog me-1"></i>操作
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('このキャストを削除しますか？')">
                                                            <input type="hidden" name="cast_id" value="<?php echo $cast['id']; ?>">
                                                            <button type="submit" name="delete_cast" class="dropdown-item text-danger">
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

                            <!-- 編集モーダル -->
                            <div class="modal fade" id="editCastModal<?php echo $cast['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">キャスト情報を編集</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="cast_id" value="<?php echo $cast['id']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label for="name<?php echo $cast['id']; ?>" class="form-label">キャスト名 <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="name<?php echo $cast['id']; ?>" name="name" 
                                                           value="<?php echo htmlspecialchars($cast['name']); ?>" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="age<?php echo $cast['id']; ?>" class="form-label">年齢</label>
                                                    <input type="number" class="form-control" id="age<?php echo $cast['id']; ?>" name="age" 
                                                           min="16" max="99" value="<?php echo $cast['age']; ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="hobby<?php echo $cast['id']; ?>" class="form-label">趣味・特技</label>
                                                    <textarea class="form-control" id="hobby<?php echo $cast['id']; ?>" name="hobby" 
                                                              rows="3"><?php echo htmlspecialchars($cast['hobby']); ?></textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="special_skill<?php echo $cast['id']; ?>" class="form-label">特技・専門</label>
                                                    <input type="text" class="form-control" id="special_skill<?php echo $cast['id']; ?>" name="special_skill" 
                                                           value="<?php echo htmlspecialchars($cast['special_skill']); ?>">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="status<?php echo $cast['id']; ?>" class="form-label">ステータス</label>
                                                    <select class="form-select" id="status<?php echo $cast['id']; ?>" name="status">
                                                        <option value="active" <?php echo $cast['status'] === 'active' ? 'selected' : ''; ?>>在籍中</option>
                                                        <option value="inactive" <?php echo $cast['status'] === 'inactive' ? 'selected' : ''; ?>>非アクティブ</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                                <button type="submit" name="update_cast" class="btn btn-primary">
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
<?php
ob_end_flush();
?>
