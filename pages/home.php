<?php
$page_title = 'ホーム';
$page_description = 'コンカフェ専門の求人・集客サイト。全国のコンカフェ・メンズコンカフェから働きたい・楽しみたいお店を検索できます。';

// 統計データの取得（安全な方法）
$stats = [
    'total_jobs' => 0,
    'total_shops' => 0,
    'total_casts' => 0,
    'total_applications' => 0
];

try {
    $stats['total_jobs'] = $db->fetch("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'")['count'];
    $stats['total_shops'] = $db->fetch("SELECT COUNT(*) as count FROM shops WHERE status = 'active'")['count'];
    $stats['total_casts'] = $db->fetch("SELECT COUNT(*) as count FROM casts WHERE status = 'active'")['count'];
    $stats['total_applications'] = $db->fetch("SELECT COUNT(*) as count FROM applications")['count'];
} catch (Exception $e) {
    // エラーが発生した場合は0のまま
}

// 人気求人ランキング（データが存在する場合のみ）
$popular_jobs = [];
try {
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
} catch (Exception $e) {
    $popular_jobs = [];
}

// 人気店舗ランキング（データが存在する場合のみ）
$popular_shops = [];
try {
    $popular_shops = $db->fetchAll(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name,
                COUNT(j.id) as job_count,
                COUNT(r.id) as review_count
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
} catch (Exception $e) {
    $popular_shops = [];
}

// ログインユーザーの未読メッセージ数
$unread_message_count = 0;
if (is_logged_in()) {
    $unread_message_count = get_unread_message_count($_SESSION['user_id'], 'user');
}

// 最新の求人情報
$latest_jobs = [];
try {
    $latest_jobs = get_jobs(null, 6);
} catch (Exception $e) {
    $latest_jobs = [];
}

// 最新の店舗情報
$latest_shops = [];
try {
    $latest_shops = $db->fetchAll(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name
         FROM shops s
         LEFT JOIN prefectures p ON s.prefecture_id = p.id
         LEFT JOIN cities c ON s.city_id = c.id
         WHERE s.status = 'active'
         ORDER BY s.created_at DESC
         LIMIT 6"
    );
} catch (Exception $e) {
    $latest_shops = [];
}

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
                    <?php if (is_logged_in() && $unread_message_count > 0): ?>
                        <a href="?page=chat" class="btn btn-warning btn-lg">
                            <i class="fas fa-comments me-2"></i>未読メッセージ
                            <span class="badge bg-danger ms-2"><?php echo $unread_message_count; ?></span>
                        </a>
                    <?php endif; ?>
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
                            <div class="stats-label">掲載店舗数</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number"><?php echo number_format($stats['total_casts']); ?></div>
                            <div class="stats-label">キャスト数</div>
                        </div>
                    </div>
                    <div class="col-6">
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

<!-- 検索セクション -->
<section class="py-5">
    <div class="container">
        <div class="search-form">
            <h3 class="text-center mb-4">
                <i class="fas fa-search me-2"></i>求人・お店を検索
            </h3>
            <form method="GET" action="?page=search">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="keyword" class="form-label">キーワード</label>
                        <input type="text" class="form-control" id="keyword" name="keyword" 
                               placeholder="店舗名、職種、エリアなど">
                    </div>
                    <div class="col-md-3">
                        <label for="prefecture" class="form-label">都道府県</label>
                        <select class="form-select" id="prefecture" name="prefecture">
                            <option value="">選択してください</option>
                            <?php
                            $prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id");
                            foreach ($prefectures as $prefecture): ?>
                                <option value="<?php echo $prefecture['id']; ?>">
                                    <?php echo htmlspecialchars($prefecture['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="concept_type" class="form-label">コンセプト</label>
                        <select class="form-select" id="concept_type" name="concept_type">
                            <option value="">すべて</option>
                            <option value="maid">メイドカフェ</option>
                            <option value="butler">執事喫茶</option>
                            <option value="idol">アイドルカフェ</option>
                            <option value="cosplay">コスプレカフェ</option>
                            <option value="other">その他</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>検索
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- 人気ランキングセクション -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-trophy me-2 text-warning"></i>人気求人ランキング
                </h3>
                <?php if (empty($popular_jobs)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">まだ求人情報がありません</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($popular_jobs as $index => $job): ?>
                        <div class="ranking-item">
                            <div class="ranking-number"><?php echo $index + 1; ?></div>
                            <div class="ranking-content">
                                <div class="ranking-title">
                                    <a href="?page=job_detail&id=<?php echo $job['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($job['title']); ?>
                                    </a>
                                </div>
                                <div class="ranking-subtitle">
                                    <?php echo htmlspecialchars($job['shop_name']); ?> | 
                                    <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="text-center mt-3">
                    <a href="?page=jobs" class="btn btn-outline-primary">
                        すべての求人を見る <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-star me-2 text-warning"></i>人気店舗ランキング
                </h3>
                <?php if (empty($popular_shops)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">まだ店舗情報がありません</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($popular_shops as $index => $shop): ?>
                        <div class="ranking-item">
                            <div class="ranking-number"><?php echo $index + 1; ?></div>
                            <div class="ranking-content">
                                <div class="ranking-title">
                                    <a href="?page=shop_detail&id=<?php echo $shop['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($shop['name']); ?>
                                    </a>
                                </div>
                                <div class="ranking-subtitle">
                                    <?php echo htmlspecialchars($shop['prefecture_name'] . $shop['city_name']); ?> | 
                                    <span class="badge badge-concept"><?php echo htmlspecialchars($shop['concept_type']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="text-center mt-3">
                    <a href="?page=shops" class="btn btn-outline-primary">
                        すべてのお店を見る <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 最新情報セクション -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-clock me-2"></i>最新の求人情報
                </h3>
                <div class="row">
                    <?php if (empty($latest_jobs)): ?>
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">まだ求人情報がありません</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($latest_jobs as $job): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="?page=job_detail&id=<?php echo $job['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($job['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="card-text small">
                                            <?php echo htmlspecialchars($job['shop_name']); ?><br>
                                            <span class="text-muted">
                                                <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name']); ?>
                                            </span>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo time_ago($job['created_at']); ?>
                                            </small>
                                            <?php if ($job['salary_min']): ?>
                                                <span class="badge badge-salary">
                                                    <?php echo number_format($job['salary_min']); ?>円〜
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-store me-2"></i>最新のお店情報
                </h3>
                <div class="row">
                    <?php if (empty($latest_shops)): ?>
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">まだ店舗情報がありません</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($latest_shops as $shop): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <?php if ($shop['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($shop['image_url']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($shop['name']); ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="?page=shop_detail&id=<?php echo $shop['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($shop['name']); ?>
                                            </a>
                                        </h6>
                                        <p class="card-text small">
                                            <?php echo htmlspecialchars(mb_substr($shop['description'], 0, 50)); ?>...
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($shop['prefecture_name'] . $shop['city_name']); ?>
                                            </small>
                                            <span class="badge badge-concept">
                                                <?php echo htmlspecialchars($shop['concept_type']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA セクション -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h3 class="mb-3">今すぐ始めよう！</h3>
        <p class="mb-4">理想のコンカフェで働く、またはお気に入りのお店を見つけよう</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <?php if (!is_logged_in()): ?>
                <a href="?page=register" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>無料会員登録
                </a>
            <?php endif; ?>
            <a href="?page=jobs" class="btn btn-outline-light btn-lg">
                <i class="fas fa-briefcase me-2"></i>求人を探す
            </a>
            <a href="?page=shops" class="btn btn-outline-light btn-lg">
                <i class="fas fa-store me-2"></i>お店を探す
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>

