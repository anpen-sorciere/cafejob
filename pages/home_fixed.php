<?php
$page_title = 'ホーム';
$page_description = 'コンカフェ専門の求人・集客サイト。全国のコンカフェ・メンズコンカフェから働きたい・楽しみたいお店を検索できます。';

// 統計データの取得
$stats = [
    'total_jobs' => $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'")['count'],
    'total_shops' => $db->fetch("SELECT COUNT(*) as count FROM shops WHERE status = 'active'")['count'],
    'total_casts' => $db->fetch("SELECT COUNT(*) as count FROM casts WHERE status = 'active'")['count'],
    'total_applications' => $db->fetch("SELECT COUNT(*) as count FROM applications")['count']
];

// 人気求人ランキング
$popular_jobs = $db->fetchAll(
    "SELECT j.*, s.name as shop_name, s.address, p.name as prefecture_name, c.name as city_name,
            COUNT(a.id) as application_count
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     LEFT JOIN applications a ON j.id = a.job_id
     WHERE j.status = 'active'
     GROUP BY j.id
     ORDER BY application_count DESC, j.created_at DESC
     LIMIT 5"
);

// 人気店舗ランキング
$popular_shops = $db->fetchAll(
    "SELECT s.*, p.name as prefecture_name, c.name as city_name,
            COUNT(DISTINCT j.id) as job_count,
            COUNT(DISTINCT r.id) as review_count
     FROM shops s
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     LEFT JOIN jobs j ON s.id = j.shop_id AND j.status = 'active'
     LEFT JOIN reviews r ON s.id = r.shop_id AND r.status = 'approved'
     WHERE s.status = 'active'
     GROUP BY s.id
     ORDER BY job_count DESC, review_count DESC
     LIMIT 5"
);

// 最新の求人情報（get_jobs関数の代わりに直接クエリ）
$latest_jobs = $db->fetchAll(
    "SELECT j.*, s.name as shop_name, s.address, p.name as prefecture_name, c.name as city_name
     FROM jobs j
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE j.status = 'active' AND s.status = 'active'
     ORDER BY j.created_at DESC
     LIMIT 6"
);

// 最新の店舗情報
$latest_shops = $db->fetchAll(
    "SELECT s.*, p.name as prefecture_name, c.name as city_name
     FROM shops s
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE s.status = 'active'
     ORDER BY s.created_at DESC
     LIMIT 6"
);

ob_start();
?>

<!-- ヒーローセクション -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">コンカフェ検索NO.1</h1>
                <h2 class="hero-title"><?php echo SITE_NAME; ?></h2>
                <p class="hero-subtitle">
                    全国のコンカフェ・メンズコンカフェから「働きたい」「楽しみたい」<br>
                    お店のエリアから検索できるコンカフェ専門のポータルサイトです。
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="?page=jobs" class="btn btn-light btn-lg">
                        <i class="fas fa-briefcase me-2"></i>求人を探す
                    </a>
                    <a href="?page=shops" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-store me-2"></i>お店を探す
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_jobs']); ?></div>
                            <div class="stats-label">求人数</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_shops']); ?></div>
                            <div class="stats-label">店舗数</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_casts']); ?></div>
                            <div class="stats-label">キャスト数</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_applications']); ?></div>
                            <div class="stats-label">応募数</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 人気求人ランキング -->
<?php if (!empty($popular_jobs)): ?>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title text-center mb-5">
                    <i class="fas fa-crown me-2 text-warning"></i>人気求人ランキング
                </h2>
            </div>
        </div>
        <div class="row">
            <?php foreach ($popular_jobs as $index => $job): ?>
            <div class="col-lg-6 mb-4">
                <div class="card job-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <span class="rank-badge rank-<?php echo $index + 1; ?>"><?php echo $index + 1; ?></span>
                                <?php echo htmlspecialchars($job['title']); ?>
                            </h5>
                            <span class="badge bg-primary"><?php echo $job['job_type']; ?></span>
                        </div>
                        <p class="text-muted mb-2">
                            <i class="fas fa-store me-1"></i><?php echo htmlspecialchars($job['shop_name']); ?>
                        </p>
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name'] . $job['address']); ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="salary-info">
                                <?php if ($job['salary_min'] || $job['salary_max']): ?>
                                    <span class="salary-text">
                                        <?php echo format_salary($job['salary_min'], $job['salary_max']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="salary-text">要相談</span>
                                <?php endif; ?>
                            </div>
                            <div class="application-count">
                                <i class="fas fa-users me-1"></i>
                                <?php echo $job['application_count']; ?>件の応募
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="?page=jobs&id=<?php echo $job['id']; ?>" class="btn btn-primary w-100">
                            詳細を見る
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 最新の求人情報 -->
<?php if (!empty($latest_jobs)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title text-center mb-5">
                    <i class="fas fa-clock me-2 text-info"></i>最新の求人情報
                </h2>
            </div>
        </div>
        <div class="row">
            <?php foreach ($latest_jobs as $job): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card job-card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-store me-1"></i><?php echo htmlspecialchars($job['shop_name']); ?>
                        </p>
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name']); ?>
                        </p>
                        <div class="salary-info mb-3">
                            <?php if ($job['salary_min'] || $job['salary_max']): ?>
                                <span class="salary-text">
                                    <?php echo format_salary($job['salary_min'], $job['salary_max']); ?>
                                </span>
                            <?php else: ?>
                                <span class="salary-text">要相談</span>
                            <?php endif; ?>
                        </div>
                        <p class="card-text">
                            <?php echo htmlspecialchars(mb_substr($job['description'], 0, 100)); ?>...
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo time_ago($job['created_at']); ?>
                            </small>
                            <a href="?page=jobs&id=<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                詳細を見る
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="?page=jobs" class="btn btn-primary btn-lg">
                すべての求人を見る
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 最新の店舗情報 -->
<?php if (!empty($latest_shops)): ?>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title text-center mb-5">
                    <i class="fas fa-store me-2 text-success"></i>最新の店舗情報
                </h2>
            </div>
        </div>
        <div class="row">
            <?php foreach ($latest_shops as $shop): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shop-card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($shop['name']); ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars($shop['prefecture_name'] . $shop['city_name']); ?>
                        </p>
                        <p class="card-text">
                            <?php echo htmlspecialchars(mb_substr($shop['description'], 0, 100)); ?>...
                        </p>
                        <div class="shop-info">
                            <?php if ($shop['concept_type']): ?>
                                <span class="badge bg-secondary me-2"><?php echo $shop['concept_type']; ?></span>
                            <?php endif; ?>
                            <?php if ($shop['uniform_type']): ?>
                                <span class="badge bg-info"><?php echo $shop['uniform_type']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo time_ago($shop['created_at']); ?>
                            </small>
                            <a href="?page=shops&id=<?php echo $shop['id']; ?>" class="btn btn-outline-success btn-sm">
                                詳細を見る
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="?page=shops" class="btn btn-success btn-lg">
                すべての店舗を見る
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>



