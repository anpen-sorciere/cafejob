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

$page_title = '求人投稿';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// 求人制限のチェック
$current_jobs = $db->fetchAll("SELECT id FROM jobs WHERE shop_id = ?", [$shop_id]);
$total_jobs = count($current_jobs);
$basic_job_limit = 1; // 基本料金に含まれる求人数

// 基本求人制限に達している場合はリダイレクト
if ($total_jobs >= $basic_job_limit) {
    $_SESSION['error_message'] = '基本求人制限に達しています。追加求人はオプション課金が必要です。';
    header('Location: jobs.php');
    exit;
}

// 求人投稿処理
if ($_POST && isset($_POST['create_job'])) {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $requirements = sanitize_input($_POST['requirements']);
    $benefits = sanitize_input($_POST['benefits']);
    $work_hours = sanitize_input($_POST['work_hours']);
    $salary_min = !empty($_POST['salary_min']) ? (int)$_POST['salary_min'] : null;
    $salary_max = !empty($_POST['salary_max']) ? (int)$_POST['salary_max'] : null;
    $location = sanitize_input($_POST['location']);
    $status = sanitize_input($_POST['status']);
    
    $errors = [];
    
    // バリデーション
    if (empty($title)) $errors[] = '求人タイトルを入力してください';
    if (empty($description)) $errors[] = '仕事内容を入力してください';
    if (empty($work_hours)) $errors[] = '勤務時間を入力してください';
    if (!$salary_min || !$salary_max) {
        $errors[] = '給与の最低額と最高額を入力してください';
    }
    
    if (empty($errors)) {
        try {
            $db->query(
                "INSERT INTO jobs (shop_id, title, description, requirements, benefits, work_hours, 
                                 salary_min, salary_max, location, status, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
                [$shop_id, $title, $description, $requirements, $benefits, $work_hours, 
                 $salary_min, $salary_max, $location, $status]
            );
            
            $_SESSION['success_message'] = '求人を投稿しました。';
            header('Location: jobs.php');
            exit;
            
        } catch (Exception $e) {
            $errors[] = '求人投稿中にエラーが発生しました: ' . $e->getMessage();
        }
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0">
                            <i class="fas fa-plus me-2"></i>新しい求人を投稿
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <!-- 基本情報 -->
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>基本情報
                            </h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">求人タイトル <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="例: 明るい笑顔でお客様をお迎えするスタッフ募集"
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                                <div class="invalid-feedback">求人タイトルを入力してください</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">仕事内容 <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required
                                          placeholder="具体的な仕事内容を詳しく記載してください"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                <div class="invalid-feedback">仕事内容を入力してください</div>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label">応募条件</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="3"
                                          placeholder="年齢、経験、資格などの条件があれば記載してください"><?php echo htmlspecialchars($_POST['requirements'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="benefits" class="form-label">福利厚生・特典</label>
                                <textarea class="form-control" id="benefits" name="benefits" rows="3"
                                          placeholder="交通費支給、食事補助、研修制度など"><?php echo htmlspecialchars($_POST['benefits'] ?? ''); ?></textarea>
                            </div>

                            <!-- 勤務条件 -->
                            <h5 class="mb-3 mt-4">
                                <i class="fas fa-clock me-2"></i>勤務条件
                            </h5>

                            <div class="mb-3">
                                <label for="work_hours" class="form-label">勤務時間 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="work_hours" name="work_hours" 
                                       placeholder="例: 10:00-18:00、シフト制"
                                       value="<?php echo htmlspecialchars($_POST['work_hours'] ?? ''); ?>" required>
                                <div class="invalid-feedback">勤務時間を入力してください</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="salary_min" class="form-label">最低給与 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="salary_min" name="salary_min" 
                                               min="0" step="1" required
                                               value="<?php echo htmlspecialchars($_POST['salary_min'] ?? ''); ?>">
                                        <span class="input-group-text">円</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="salary_max" class="form-label">最高給与 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="salary_max" name="salary_max" 
                                               min="0" step="1" required
                                               value="<?php echo htmlspecialchars($_POST['salary_max'] ?? ''); ?>">
                                        <span class="input-group-text">円</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">勤務地</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="例: 大阪市浪速区なんば"
                                       value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                            </div>

                            <!-- 公開設定 -->
                            <h5 class="mb-3 mt-4">
                                <i class="fas fa-eye me-2"></i>公開設定
                            </h5>

                            <div class="mb-4">
                                <label for="status" class="form-label">公開状態</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>公開する</option>
                                    <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>下書きとして保存</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="create_job" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>求人を投稿
                                </button>
                                <a href="jobs.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>求人一覧に戻る
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrapバリデーション
        const form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
