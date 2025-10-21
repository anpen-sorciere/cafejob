<?php
$page_title = '最新情報';
$page_description = 'コンカフェの最新情報をリアルタイムで表示します。';

// 最新の求人情報（30分以内）
$recent_jobs = $db->fetchAll(
    "SELECT j.*, s.name as shop_name, s.concept_type, s.image_url,
            p.name as prefecture_name, c.name as city_name
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE j.status = 'active' AND s.status = 'active' 
     AND j.created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
     ORDER BY j.created_at DESC
     LIMIT 20"
);

// 最新の店舗情報（1時間以内）
$recent_shops = $db->fetchAll(
    "SELECT s.*, p.name as prefecture_name, c.name as city_name
     FROM shops s
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE s.status = 'active' 
     AND s.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
     ORDER BY s.created_at DESC
     LIMIT 10"
);

// 最新の応募情報（1時間以内）
$recent_applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, s.name as shop_name, u.username
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN shops s ON j.shop_id = s.id
     JOIN users u ON a.user_id = u.id
     WHERE a.applied_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
     ORDER BY a.applied_at DESC
     LIMIT 10"
);

// 最新のキャスト情報（2時間以内）
$recent_casts = $db->fetchAll(
    "SELECT c.*, s.name as shop_name, s.concept_type
     FROM casts c
     JOIN shops s ON c.shop_id = s.id
     WHERE c.status = 'active' AND s.status = 'active'
     AND c.created_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
     ORDER BY c.created_at DESC
     LIMIT 10"
);

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-clock me-2"></i>最新情報
                <small class="text-muted">リアルタイム更新</small>
            </h1>
        </div>
    </div>
    
    <!-- 統計情報 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?php echo count($recent_jobs); ?></h3>
                    <p class="mb-0">30分以内の新着求人</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?php echo count($recent_shops); ?></h3>
                    <p class="mb-0">1時間以内の新着店舗</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info"><?php echo count($recent_applications); ?></h3>
                    <p class="mb-0">1時間以内の応募</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning"><?php echo count($recent_casts); ?></h3>
                    <p class="mb-0">2時間以内の新着キャスト</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- 最新求人情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>最新求人情報
                        <span class="badge bg-light text-primary ms-2"><?php echo count($recent_jobs); ?>件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($recent_jobs)): ?>
                        <p class="text-muted text-center">30分以内の新着求人はありません</p>
                    <?php else: ?>
                        <?php foreach ($recent_jobs as $job): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="?page=job_detail&id=<?php echo $job['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        <?php echo htmlspecialchars($job['shop_name']); ?> | 
                                        <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name']); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo time_ago($job['created_at']); ?>
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($job['concept_type']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- 最新店舗情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>最新店舗情報
                        <span class="badge bg-light text-success ms-2"><?php echo count($recent_shops); ?>件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($recent_shops)): ?>
                        <p class="text-muted text-center">1時間以内の新着店舗はありません</p>
                    <?php else: ?>
                        <?php foreach ($recent_shops as $shop): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="?page=shop_detail&id=<?php echo $shop['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($shop['name']); ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        <?php echo htmlspecialchars($shop['prefecture_name'] . $shop['city_name']); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo time_ago($shop['created_at']); ?>
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success"><?php echo htmlspecialchars($shop['concept_type']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- 最新応募情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>最新応募情報
                        <span class="badge bg-light text-info ms-2"><?php echo count($recent_applications); ?>件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($recent_applications)): ?>
                        <p class="text-muted text-center">1時間以内の応募はありません</p>
                    <?php else: ?>
                        <?php foreach ($recent_applications as $application): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="?page=job_detail&id=<?php echo $application['job_id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($application['job_title']); ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        <?php echo htmlspecialchars($application['shop_name']); ?> | 
                                        <?php echo htmlspecialchars($application['username']); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo time_ago($application['applied_at']); ?>
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-info">応募</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- 最新キャスト情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>最新キャスト情報
                        <span class="badge bg-light text-warning ms-2"><?php echo count($recent_casts); ?>件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($recent_casts)): ?>
                        <p class="text-muted text-center">2時間以内の新着キャストはありません</p>
                    <?php else: ?>
                        <?php foreach ($recent_casts as $cast): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="?page=cast_detail&id=<?php echo $cast['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($cast['name']); ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        <?php echo htmlspecialchars($cast['shop_name']); ?>
                                        <?php if ($cast['age']): ?>
                                            | <?php echo $cast['age']; ?>歳
                                        <?php endif; ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo time_ago($cast['created_at']); ?>
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($cast['concept_type']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 自動更新情報 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted mb-2">
                        <i class="fas fa-sync-alt me-1"></i>
                        このページは30秒ごとに自動更新されます
                    </p>
                    <button class="btn btn-outline-primary" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>今すぐ更新
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 30秒ごとに自動更新
setInterval(function() {
    location.reload();
}, 30000);

// ページ読み込み時の時刻表示
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    console.log('ページ読み込み時刻:', now.toLocaleString());
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
