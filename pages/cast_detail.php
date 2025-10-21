<?php
$page_title = 'キャスト詳細';
$page_description = 'キャストの詳細情報を表示します。';

// キャストIDの取得
$cast_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$cast_id) {
    header('Location: ?page=cast');
    exit;
}

// キャスト詳細の取得
$cast = $db->fetch(
    "SELECT c.*, s.name as shop_name, s.description as shop_description, s.address, s.phone, s.email, s.website, 
            s.opening_hours, s.concept_type, s.uniform_type, s.image_url as shop_image,
            p.name as prefecture_name, city.name as city_name
     FROM casts c
     JOIN shops s ON c.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities city ON s.city_id = city.id
     WHERE c.id = ? AND c.status = 'active' AND s.status = 'active'",
    [$cast_id]
);

if (!$cast) {
    header('Location: ?page=cast');
    exit;
}

// 同じ店舗の他のキャスト
$related_casts = $db->fetchAll(
    "SELECT c.*, s.name as shop_name, s.concept_type
     FROM casts c
     JOIN shops s ON c.shop_id = s.id
     WHERE c.shop_id = ? AND c.id != ? AND c.status = 'active' AND s.status = 'active'
     ORDER BY c.created_at DESC
     LIMIT 3",
    [$cast['shop_id'], $cast_id]
);

// 店舗の求人情報
$shop_jobs = $db->fetchAll(
    "SELECT j.*, s.name as shop_name, s.concept_type
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     WHERE j.shop_id = ? AND j.status = 'active' AND s.status = 'active'
     ORDER BY j.created_at DESC
     LIMIT 3",
    [$cast['shop_id']]
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
                    <li class="breadcrumb-item"><a href="?page=cast">キャスト一覧</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($cast['name']); ?></li>
                </ol>
            </nav>
            
            <!-- キャスト詳細 -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if ($cast['profile_image']): ?>
                                <img src="<?php echo htmlspecialchars($cast['profile_image']); ?>" 
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($cast['name']); ?>">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 300px;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h3 mb-1"><?php echo htmlspecialchars($cast['name']); ?></h1>
                                    <?php if ($cast['nickname']): ?>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo htmlspecialchars($cast['nickname']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-store me-1"></i>
                                        <?php echo htmlspecialchars($cast['shop_name']); ?>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($cast['concept_type']); ?></span>
                                </div>
                            </div>
                            
                            <!-- 基本情報 -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3">基本情報</h5>
                                    <table class="table table-sm">
                                        <?php if ($cast['age']): ?>
                                        <tr>
                                            <td><strong>年齢</strong></td>
                                            <td><?php echo $cast['age']; ?>歳</td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($cast['height']): ?>
                                        <tr>
                                            <td><strong>身長</strong></td>
                                            <td><?php echo $cast['height']; ?>cm</td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($cast['blood_type']): ?>
                                        <tr>
                                            <td><strong>血液型</strong></td>
                                            <td><?php echo $cast['blood_type']; ?>型</td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong>所属店舗</strong></td>
                                            <td><?php echo htmlspecialchars($cast['shop_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>登録日</strong></td>
                                            <td><?php echo format_date($cast['created_at']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">プロフィール</h5>
                                    <?php if ($cast['hobby']): ?>
                                    <div class="mb-3">
                                        <h6 class="small text-muted mb-1">趣味</h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($cast['hobby']); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($cast['special_skill']): ?>
                                    <div class="mb-3">
                                        <h6 class="small text-muted mb-1">特技</h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($cast['special_skill']); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 店舗情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>所属店舗情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><?php echo htmlspecialchars($cast['shop_name']); ?></h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($cast['prefecture_name'] . $cast['city_name'] . $cast['address']); ?>
                            </p>
                            <?php if ($cast['phone']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-phone me-1"></i>
                                <?php echo htmlspecialchars($cast['phone']); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($cast['website']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-globe me-1"></i>
                                <a href="<?php echo htmlspecialchars($cast['website']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($cast['website']); ?>
                                </a>
                            </p>
                            <?php endif; ?>
                            <?php if ($cast['opening_hours']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock me-1"></i>
                                <pre class="mb-0 small"><?php echo htmlspecialchars($cast['opening_hours']); ?></pre>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <?php if ($cast['shop_image']): ?>
                                <img src="<?php echo htmlspecialchars($cast['shop_image']); ?>" 
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($cast['shop_name']); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 同じ店舗の他のキャスト -->
            <?php if (!empty($related_casts)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>同じ店舗の他のキャスト
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($related_casts as $related_cast): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <?php if ($related_cast['profile_image']): ?>
                                    <img src="<?php echo htmlspecialchars($related_cast['profile_image']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($related_cast['name']); ?>">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 150px;">
                                        <i class="fas fa-user fa-2x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="?page=cast_detail&id=<?php echo $related_cast['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($related_cast['name']); ?>
                                        </a>
                                    </h6>
                                    <?php if ($related_cast['nickname']): ?>
                                        <p class="card-text small text-muted">
                                            <?php echo htmlspecialchars($related_cast['nickname']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($related_cast['age']): ?>
                                        <p class="card-text small"><?php echo $related_cast['age']; ?>歳</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 店舗の求人情報 -->
            <?php if (!empty($shop_jobs)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>店舗の求人情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($shop_jobs as $job): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="?page=job_detail&id=<?php echo $job['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="card-text small text-muted">
                                        <?php echo htmlspecialchars($job['shop_name']); ?>
                                    </p>
                                    <?php if ($job['salary_min']): ?>
                                        <span class="badge bg-primary">
                                            <?php echo number_format($job['salary_min']); ?>円〜
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
            <!-- お気に入り -->
            <?php if (is_logged_in()): ?>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <button class="btn btn-outline-danger favorite-btn" 
                            data-item-id="<?php echo $cast['id']; ?>" 
                            data-item-type="cast">
                        <i class="far fa-heart me-1"></i>お気に入りに追加
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- シェア -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-share-alt me-2"></i>シェア
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($cast['name'] . ' - ' . $cast['shop_name']); ?>&url=<?php echo urlencode(SITE_URL . '?page=cast_detail&id=' . $cast_id); ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '?page=cast_detail&id=' . $cast_id); ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- 店舗情報 -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>店舗情報
                    </h6>
                </div>
                <div class="card-body">
                    <h6><?php echo htmlspecialchars($cast['shop_name']); ?></h6>
                    <p class="small text-muted mb-2">
                        <?php echo htmlspecialchars($cast['prefecture_name'] . $cast['city_name']); ?>
                    </p>
                    <p class="small mb-2">
                        <span class="badge bg-primary"><?php echo htmlspecialchars($cast['concept_type']); ?></span>
                    </p>
                    <a href="?page=shop_detail&id=<?php echo $cast['shop_id']; ?>" class="btn btn-outline-primary btn-sm">
                        店舗詳細を見る
                    </a>
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
