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

$page_title = '応募詳細';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// 応募IDの取得
$application_id = (int)($_GET['id'] ?? 0);

if (!$application_id) {
    header('Location: applications.php');
    exit;
}

// 応募詳細の取得
$application = $db->fetch(
    "SELECT a.*, j.title as job_title, j.description as job_description, j.id as job_id,
            u.username, u.email, u.phone, u.first_name, u.last_name, u.created_at as user_created_at,
            u.birth_date, u.gender, u.prefecture_id, u.city_id, u.address,
            p.name as prefecture_name, c.name as city_name
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN users u ON a.user_id = u.id
     LEFT JOIN prefectures p ON u.prefecture_id = p.id
     LEFT JOIN cities c ON u.city_id = c.id
     WHERE a.id = ? AND j.shop_id = ?",
    [$application_id, $shop_id]
);

if (!$application) {
    header('Location: applications.php');
    exit;
}

// 応募ステータス更新処理
if ($_POST && isset($_POST['update_status'])) {
    $new_status = sanitize_input($_POST['status']);
    $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');
    
    if (in_array($new_status, ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])) {
        $db->query(
            "UPDATE applications SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?",
            [$new_status, $admin_notes, $application_id]
        );
        $_SESSION['success_message'] = '応募ステータスを更新しました。';
        header('Location: application_detail.php?id=' . $application_id);
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
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-alt me-2"></i>応募詳細
                    </h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-<?php 
                            echo $application['status'] === 'pending' ? 'warning' : 
                                ($application['status'] === 'reviewed' ? 'info' : 
                                    ($application['status'] === 'interview' ? 'primary' : 
                                        ($application['status'] === 'accepted' ? 'success' : 'danger'))); 
                        ?> fs-6">
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
                        <a href="applications.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>応募一覧に戻る
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- 応募者情報 -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>応募者情報
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>お名前</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>ユーザー名</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        @<?php echo htmlspecialchars($application['username']); ?>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>メールアドレス</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <a href="mailto:<?php echo htmlspecialchars($application['email']); ?>" class="text-decoration-none">
                                            <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($application['email']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php if (!empty($application['phone'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>電話番号</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <a href="tel:<?php echo htmlspecialchars($application['phone']); ?>" class="text-decoration-none">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($application['phone']); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($application['birth_date'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>生年月日</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php echo date('Y年m月d日', strtotime($application['birth_date'])); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($application['gender'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>性別</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php echo htmlspecialchars($application['gender']); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($application['prefecture_name']) || !empty($application['city_name']) || !empty($application['address'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>住所</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php 
                                            $address_parts = array_filter([
                                                $application['prefecture_name'],
                                                $application['city_name'],
                                                $application['address']
                                            ]);
                                            echo htmlspecialchars(implode('', $address_parts));
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>登録日</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <?php echo date('Y年m月d日', strtotime($application['user_created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 求人情報・応募詳細 -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-briefcase me-2"></i>求人情報
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="mb-3"><?php echo htmlspecialchars($application['job_title']); ?></h6>
                                <p class="text-muted small"><?php echo nl2br(htmlspecialchars($application['job_description'])); ?></p>
                                <div class="text-end">
                                    <a href="jobs.php" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i>求人詳細
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-comment me-2"></i>応募メッセージ
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($application['message'])): ?>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($application['message'])); ?></p>
                                <?php else: ?>
                                    <p class="text-muted mb-0">メッセージはありません</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ステータス管理 -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-cog me-2"></i>ステータス管理
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">ステータス</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>未読</option>
                                                <option value="reviewed" <?php echo $application['status'] === 'reviewed' ? 'selected' : ''; ?>>読了</option>
                                                <option value="interview" <?php echo $application['status'] === 'interview' ? 'selected' : ''; ?>>面接</option>
                                                <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>採用</option>
                                                <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>不採用</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_notes" class="form-label">管理者メモ</label>
                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                                      placeholder="面接日時、採用理由、不採用理由などを記録してください"><?php echo htmlspecialchars($application['admin_notes'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    応募日時: <?php echo date('Y年m月d日 H:i', strtotime($application['applied_at'])); ?>
                                                    <?php if ($application['updated_at'] !== $application['applied_at']): ?>
                                                        | 更新日時: <?php echo date('Y年m月d日 H:i', strtotime($application['updated_at'])); ?>
                                                    <?php endif; ?>
                                                </small>
                                                <button type="submit" name="update_status" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>ステータス更新
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
