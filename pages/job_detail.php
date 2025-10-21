<?php
$page_title = '求人詳細';
$page_description = '求人の詳細情報を表示します。';

// 求人IDの取得
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$job_id) {
    header('Location: ?page=jobs');
    exit;
}

// 求人詳細の取得
$job = $db->fetch(
    "SELECT j.*, s.name as shop_name, s.description as shop_description, s.address, s.phone, s.email, s.website, 
            s.opening_hours, s.concept_type, s.uniform_type, s.image_url as shop_image,
            p.name as prefecture_name, c.name as city_name
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE j.id = ? AND j.status = 'active' AND s.status = 'active'",
    [$job_id]
);

if (!$job) {
    header('Location: ?page=jobs');
    exit;
}

// 応募処理
if ($_POST && isset($_POST['apply'])) {
    if (!is_logged_in()) {
        $_SESSION['error_message'] = '応募するにはログインが必要です。';
        header('Location: ?page=login');
        exit;
    }
    
    $message = sanitize_input($_POST['message']);
    
    // 既に応募済みかチェック
    $existing_application = $db->fetch(
        "SELECT id FROM applications WHERE job_id = ? AND user_id = ?",
        [$job_id, $_SESSION['user_id']]
    );
    
    if ($existing_application) {
        $_SESSION['error_message'] = 'この求人には既に応募済みです。';
    } else {
        // 応募データを挿入
        $db->query(
            "INSERT INTO applications (job_id, user_id, message, status, applied_at) 
             VALUES (?, ?, ?, 'pending', NOW())",
            [$job_id, $_SESSION['user_id'], $message]
        );
        
        $_SESSION['success_message'] = '応募が完了しました。店舗からの連絡をお待ちください。';
        header('Location: ?page=job_detail&id=' . $job_id);
        exit;
    }
}

// 関連求人の取得
$related_jobs = $db->fetchAll(
    "SELECT j.*, s.name as shop_name, s.concept_type, s.image_url,
            p.name as prefecture_name, c.name as city_name
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE j.shop_id = ? AND j.id != ? AND j.status = 'active' AND s.status = 'active'
     ORDER BY j.created_at DESC
     LIMIT 3",
    [$job['shop_id'], $job_id]
);

// キャスト情報の取得
$casts = $db->fetchAll(
    "SELECT * FROM casts WHERE shop_id = ? AND status = 'active' ORDER BY created_at DESC",
    [$job['shop_id']]
);

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <!-- メインコンテンツ -->
        <div class="col-lg-8">
            <!-- パンくずリスト -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?page=home">ホーム</a></li>
                    <li class="breadcrumb-item"><a href="?page=jobs">求人検索</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($job['title']); ?></li>
                </ol>
            </nav>
            
            <!-- 求人詳細 -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h4 mb-1"><?php echo htmlspecialchars($job['title']); ?></h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-store me-1"></i>
                                <?php echo htmlspecialchars($job['shop_name']); ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($job['concept_type']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- 基本情報 -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">基本情報</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>雇用形態</strong></td>
                                    <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                                </tr>
                                <?php if ($job['salary_min']): ?>
                                <tr>
                                    <td><strong>給与</strong></td>
                                    <td>
                                        <?php echo number_format($job['salary_min']); ?>円〜
                                        <?php if ($job['salary_max']): ?>
                                            <?php echo number_format($job['salary_max']); ?>円
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>性別</strong></td>
                                    <td><?php echo htmlspecialchars($job['gender_requirement']); ?></td>
                                </tr>
                                <?php if ($job['age_min'] || $job['age_max']): ?>
                                <tr>
                                    <td><strong>年齢</strong></td>
                                    <td>
                                        <?php if ($job['age_min']): ?><?php echo $job['age_min']; ?>歳〜<?php endif; ?>
                                        <?php if ($job['age_max']): ?><?php echo $job['age_max']; ?>歳<?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($job['application_deadline']): ?>
                                <tr>
                                    <td><strong>応募締切</strong></td>
                                    <td><?php echo format_date($job['application_deadline']); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">勤務時間</h5>
                            <div class="bg-light p-3 rounded">
                                <pre class="mb-0"><?php echo htmlspecialchars($job['work_hours']); ?></pre>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 求人内容 -->
                    <?php if ($job['description']): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">求人内容</h5>
                        <div class="bg-light p-3 rounded">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- 応募条件 -->
                    <?php if ($job['requirements']): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">応募条件</h5>
                        <div class="bg-light p-3 rounded">
                            <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- 福利厚生 -->
                    <?php if ($job['benefits']): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">福利厚生</h5>
                        <div class="bg-light p-3 rounded">
                            <?php echo nl2br(htmlspecialchars($job['benefits'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 店舗情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>店舗情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><?php echo htmlspecialchars($job['shop_name']); ?></h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name'] . $job['address']); ?>
                            </p>
                            <?php if ($job['phone']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-phone me-1"></i>
                                <?php echo htmlspecialchars($job['phone']); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($job['website']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-globe me-1"></i>
                                <a href="<?php echo htmlspecialchars($job['website']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($job['website']); ?>
                                </a>
                            </p>
                            <?php endif; ?>
                            <?php if ($job['opening_hours']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock me-1"></i>
                                <pre class="mb-0 small"><?php echo htmlspecialchars($job['opening_hours']); ?></pre>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <?php if ($job['shop_image']): ?>
                                <img src="<?php echo htmlspecialchars($job['shop_image']); ?>" 
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($job['shop_name']); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- キャスト情報 -->
            <?php if (!empty($casts)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>キャスト情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($casts as $cast): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <?php if ($cast['profile_image']): ?>
                                    <img src="<?php echo htmlspecialchars($cast['profile_image']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($cast['name']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($cast['name']); ?></h6>
                                    <?php if ($cast['nickname']): ?>
                                        <p class="card-text small text-muted">
                                            ニックネーム: <?php echo htmlspecialchars($cast['nickname']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($cast['age']): ?>
                                        <p class="card-text small">年齢: <?php echo $cast['age']; ?>歳</p>
                                    <?php endif; ?>
                                    <?php if ($cast['hobby']): ?>
                                        <p class="card-text small">趣味: <?php echo htmlspecialchars($cast['hobby']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 関連求人 -->
            <?php if (!empty($related_jobs)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>同じ店舗の他の求人
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($related_jobs as $related_job): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="?page=job_detail&id=<?php echo $related_job['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($related_job['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="card-text small text-muted">
                                        <?php echo htmlspecialchars($related_job['prefecture_name'] . $related_job['city_name']); ?>
                                    </p>
                                    <?php if ($related_job['salary_min']): ?>
                                        <span class="badge bg-primary">
                                            <?php echo number_format($related_job['salary_min']); ?>円〜
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- サイドバー -->
        <div class="col-lg-4">
            <!-- 応募フォーム -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>応募フォーム
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (is_logged_in()): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="message" class="form-label">応募メッセージ</label>
                                <textarea class="form-control" id="message" name="message" rows="4" 
                                          placeholder="自己紹介や応募動機を記入してください"></textarea>
                            </div>
                            <button type="submit" name="apply" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-1"></i>応募する
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted mb-3">応募するにはログインが必要です。</p>
                        <a href="?page=login" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-1"></i>ログイン
                        </a>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                アカウントをお持ちでない方は
                                <a href="?page=register">新規登録</a>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- お気に入り -->
            <?php if (is_logged_in()): ?>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <button class="btn btn-outline-danger favorite-btn" 
                            data-item-id="<?php echo $job['id']; ?>" 
                            data-item-type="job">
                        <i class="far fa-heart me-1"></i>お気に入りに追加
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- シェア -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-share-alt me-2"></i>シェア
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($job['title']); ?>&url=<?php echo urlencode(SITE_URL . '?page=job_detail&id=' . $job_id); ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '?page=job_detail&id=' . $job_id); ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('URLをコピーしました');
    });
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
