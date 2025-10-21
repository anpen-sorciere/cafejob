<?php
$page_title = 'お店検索';
$page_description = 'コンカフェのお店を検索できます。エリア、コンセプト、制服などで絞り込み検索が可能です。';

// 検索パラメータの取得
$keyword = isset($_GET['keyword']) ? sanitize_input($_GET['keyword']) : '';
$prefecture_id = isset($_GET['prefecture']) ? (int)$_GET['prefecture'] : null;
$concept_type = isset($_GET['concept_type']) ? sanitize_input($_GET['concept_type']) : null;
$uniform_type = isset($_GET['uniform_type']) ? sanitize_input($_GET['uniform_type']) : null;
$sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'created_at';
$page_num = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// 検索クエリの構築
$sql = "SELECT s.*, p.name as prefecture_name, c.name as city_name,
               COUNT(j.id) as job_count,
               COUNT(r.id) as review_count,
               AVG(r.rating) as avg_rating
        FROM shops s
        LEFT JOIN prefectures p ON s.prefecture_id = p.id
        LEFT JOIN cities c ON s.city_id = c.id
        LEFT JOIN jobs j ON s.id = j.shop_id AND j.status = 'active'
        LEFT JOIN reviews r ON s.id = r.shop_id AND r.status = 'approved'
        WHERE s.status = 'active'";

$params = [];

if ($keyword) {
    $sql .= " AND (s.name LIKE ? OR s.description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if ($prefecture_id) {
    $sql .= " AND s.prefecture_id = ?";
    $params[] = $prefecture_id;
}

if ($concept_type) {
    $sql .= " AND s.concept_type = ?";
    $params[] = $concept_type;
}

if ($uniform_type) {
    $sql .= " AND s.uniform_type LIKE ?";
    $params[] = "%$uniform_type%";
}

$sql .= " GROUP BY s.id";

// ソート処理
switch ($sort) {
    case 'rating':
        $sql .= " ORDER BY avg_rating DESC, s.created_at DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY job_count DESC, review_count DESC, s.created_at DESC";
        break;
    case 'name':
        $sql .= " ORDER BY s.name ASC";
        break;
    default:
        $sql .= " ORDER BY s.created_at DESC";
        break;
}

// 総件数の取得
$count_sql = str_replace("SELECT s.*, p.name as prefecture_name, c.name as city_name,
                               COUNT(j.id) as job_count,
                               COUNT(r.id) as review_count,
                               AVG(r.rating) as avg_rating", 
                         "SELECT COUNT(DISTINCT s.id) as total", $sql);
$count_sql = str_replace("GROUP BY s.id", "", $count_sql);
$count_sql = str_replace("ORDER BY s.created_at DESC", "", $count_sql);
$count_sql = str_replace("ORDER BY avg_rating DESC, s.created_at DESC", "", $count_sql);
$count_sql = str_replace("ORDER BY job_count DESC, review_count DESC, s.created_at DESC", "", $count_sql);
$count_sql = str_replace("ORDER BY s.name ASC", "", $count_sql);

$total_shops = $db->fetch($count_sql, $params)['total'];

// ページネーション
$pagination = paginate($total_shops, ITEMS_PER_PAGE, $page_num);
$offset = $pagination['offset'];

$sql .= " LIMIT ? OFFSET ?";
$params[] = ITEMS_PER_PAGE;
$params[] = $offset;

$shops = $db->fetchAll($sql, $params);

// 都道府県データの取得
$prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id");

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <!-- サイドバー（検索フィルター） -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>検索フィルター
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" id="filterForm">
                        <input type="hidden" name="page" value="shops">
                        
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                   value="<?php echo htmlspecialchars($keyword); ?>" 
                                   placeholder="店舗名、コンセプトなど">
                        </div>
                        
                        <div class="mb-3">
                            <label for="prefecture" class="form-label">都道府県</label>
                            <select class="form-select" id="prefecture" name="prefecture">
                                <option value="">すべて</option>
                                <?php foreach ($prefectures as $prefecture): ?>
                                    <option value="<?php echo $prefecture['id']; ?>" 
                                            <?php echo ($prefecture_id == $prefecture['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prefecture['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="concept_type" class="form-label">コンセプト</label>
                            <select class="form-select" id="concept_type" name="concept_type">
                                <option value="">すべて</option>
                                <option value="maid" <?php echo ($concept_type == 'maid') ? 'selected' : ''; ?>>メイドカフェ</option>
                                <option value="butler" <?php echo ($concept_type == 'butler') ? 'selected' : ''; ?>>執事喫茶</option>
                                <option value="idol" <?php echo ($concept_type == 'idol') ? 'selected' : ''; ?>>アイドルカフェ</option>
                                <option value="cosplay" <?php echo ($concept_type == 'cosplay') ? 'selected' : ''; ?>>コスプレカフェ</option>
                                <option value="other" <?php echo ($concept_type == 'other') ? 'selected' : ''; ?>>その他</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="uniform_type" class="form-label">制服タイプ</label>
                            <select class="form-select" id="uniform_type" name="uniform_type">
                                <option value="">すべて</option>
                                <option value="メイド" <?php echo ($uniform_type == 'メイド') ? 'selected' : ''; ?>>メイド</option>
                                <option value="執事" <?php echo ($uniform_type == '執事') ? 'selected' : ''; ?>>執事</option>
                                <option value="アイドル" <?php echo ($uniform_type == 'アイドル') ? 'selected' : ''; ?>>アイドル</option>
                                <option value="コスプレ" <?php echo ($uniform_type == 'コスプレ') ? 'selected' : ''; ?>>コスプレ</option>
                                <option value="制服" <?php echo ($uniform_type == '制服') ? 'selected' : ''; ?>>制服</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>検索
                            </button>
                            <a href="?page=shops" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>リセット
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- メインコンテンツ -->
        <div class="col-lg-9">
            <!-- 検索結果ヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">お店検索結果</h2>
                    <p class="text-muted mb-0">
                        <?php echo number_format($total_shops); ?>件のお店が見つかりました
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0">並び順:</label>
                    <select class="form-select" id="sort" style="width: auto;" onchange="sortResults(this.value)">
                        <option value="created_at" <?php echo ($sort == 'created_at') ? 'selected' : ''; ?>>新着順</option>
                        <option value="rating" <?php echo ($sort == 'rating') ? 'selected' : ''; ?>>評価順</option>
                        <option value="popular" <?php echo ($sort == 'popular') ? 'selected' : ''; ?>>人気順</option>
                        <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>名前順</option>
                    </select>
                </div>
            </div>
            
            <!-- お店一覧 -->
            <?php if (empty($shops)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">該当するお店が見つかりませんでした</h4>
                    <p class="text-muted">検索条件を変更して再度お試しください。</p>
                    <a href="?page=shops" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>条件をリセット
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($shops as $shop): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <?php if ($shop['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($shop['image_url']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($shop['name']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="?page=shop_detail&id=<?php echo $shop['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($shop['name']); ?>
                                            </a>
                                        </h5>
                                        <span class="badge badge-concept">
                                            <?php echo htmlspecialchars($shop['concept_type']); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($shop['prefecture_name'] . $shop['city_name']); ?>
                                    </p>
                                    
                                    <?php if ($shop['description']): ?>
                                        <p class="card-text small">
                                            <?php echo htmlspecialchars(mb_substr($shop['description'], 0, 100)); ?>...
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex gap-2">
                                                <?php if ($shop['avg_rating']): ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-star text-warning me-1"></i>
                                                        <span class="small"><?php echo number_format($shop['avg_rating'], 1); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($shop['job_count'] > 0): ?>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-briefcase text-primary me-1"></i>
                                                        <span class="small"><?php echo $shop['job_count']; ?>件</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo time_ago($shop['created_at']); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="?page=shop_detail&id=<?php echo $shop['id']; ?>" 
                                               class="btn btn-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>詳細を見る
                                            </a>
                                            <?php if (is_logged_in()): ?>
                                                <button class="btn btn-outline-danger btn-sm favorite-btn" 
                                                        data-item-id="<?php echo $shop['id']; ?>" 
                                                        data-item-type="shop">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- ページネーション -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="お店検索結果のページネーション">
                        <ul class="pagination">
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['prev_page']])); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $pagination['current_page'] - 2);
                            $end_page = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['next_page']])); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>

