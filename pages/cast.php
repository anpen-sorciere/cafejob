<?php
$page_title = 'キャスト一覧';
$page_description = 'コンカフェのキャスト一覧を表示します。';

// 検索パラメータの取得
$keyword = isset($_GET['keyword']) ? sanitize_input($_GET['keyword']) : '';
$shop_id = isset($_GET['shop']) ? (int)$_GET['shop'] : null;
$age_min = isset($_GET['age_min']) ? (int)$_GET['age_min'] : null;
$age_max = isset($_GET['age_max']) ? (int)$_GET['age_max'] : null;
$blood_type = isset($_GET['blood_type']) ? sanitize_input($_GET['blood_type']) : null;
$sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'created_at';
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

// 検索クエリの構築
$sql = "SELECT c.*, s.name as shop_name, s.concept_type, s.image_url as shop_image,
               p.name as prefecture_name, city.name as city_name
        FROM casts c
        JOIN shops s ON c.shop_id = s.id
        LEFT JOIN prefectures p ON s.prefecture_id = p.id
        LEFT JOIN cities city ON s.city_id = city.id
        WHERE c.status = 'active' AND s.status = 'active'";

$params = [];

if ($keyword) {
    $sql .= " AND (c.name LIKE ? OR c.nickname LIKE ? OR c.hobby LIKE ? OR c.special_skill LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if ($shop_id) {
    $sql .= " AND c.shop_id = ?";
    $params[] = $shop_id;
}

if ($age_min) {
    $sql .= " AND c.age >= ?";
    $params[] = $age_min;
}

if ($age_max) {
    $sql .= " AND c.age <= ?";
    $params[] = $age_max;
}

if ($blood_type) {
    $sql .= " AND c.blood_type = ?";
    $params[] = $blood_type;
}

// ソート処理
switch ($sort) {
    case 'age':
        $sql .= " ORDER BY c.age ASC, c.created_at DESC";
        break;
    case 'height':
        $sql .= " ORDER BY c.height DESC, c.created_at DESC";
        break;
    case 'name':
        $sql .= " ORDER BY c.name ASC, c.created_at DESC";
        break;
    default:
        $sql .= " ORDER BY c.created_at DESC";
        break;
}

// 総件数の取得
$count_sql = "SELECT COUNT(*) as total
        FROM casts c
        JOIN shops s ON c.shop_id = s.id
        LEFT JOIN prefectures p ON s.prefecture_id = p.id
        LEFT JOIN cities city ON s.city_id = city.id
        WHERE c.status = 'active' AND s.status = 'active'";

$count_params = [];

if ($keyword) {
    $count_sql .= " AND (c.name LIKE ? OR c.nickname LIKE ? OR c.hobby LIKE ? OR c.special_skill LIKE ?)";
    $count_params[] = "%$keyword%";
    $count_params[] = "%$keyword%";
    $count_params[] = "%$keyword%";
    $count_params[] = "%$keyword%";
}

if ($shop_id) {
    $count_sql .= " AND c.shop_id = ?";
    $count_params[] = $shop_id;
}

if ($age_min) {
    $count_sql .= " AND c.age >= ?";
    $count_params[] = $age_min;
}

if ($age_max) {
    $count_sql .= " AND c.age <= ?";
    $count_params[] = $age_max;
}

if ($blood_type) {
    $count_sql .= " AND c.blood_type = ?";
    $count_params[] = $blood_type;
}

$total_casts = $db->fetch($count_sql, $count_params)['total'];

// データが0件の場合は空配列を返す
if ($total_casts == 0) {
    $casts = [];
    $pagination = [
        'total_items' => 0,
        'total_pages' => 0,
        'current_page' => 1,
        'items_per_page' => 20,
        'offset' => 0,
        'has_prev' => false,
        'has_next' => false,
        'prev_page' => null,
        'next_page' => null
    ];
} else {
    // ページネーション
    $items_per_page = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 20;
    $pagination = paginate($total_casts, $items_per_page, $page_num);
    $offset = $pagination['offset'];

    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $items_per_page;
    $params[] = $offset;

    $casts = $db->fetchAll($sql, $params);
}

// 店舗データの取得（フィルター用）
$shops = $db->fetchAll("SELECT id, name FROM shops WHERE status = 'active' ORDER BY name");

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
                        <input type="hidden" name="page" value="cast">
                        
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                   value="<?php echo htmlspecialchars($keyword); ?>" 
                                   placeholder="名前、趣味、特技など">
                        </div>
                        
                        <div class="mb-3">
                            <label for="shop" class="form-label">店舗</label>
                            <select class="form-select" id="shop" name="shop">
                                <option value="">すべて</option>
                                <?php foreach ($shops as $shop): ?>
                                    <option value="<?php echo $shop['id']; ?>" 
                                            <?php echo ($shop_id == $shop['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($shop['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="age_min" class="form-label">年齢（最小）</label>
                                <input type="number" class="form-control" id="age_min" name="age_min" 
                                       value="<?php echo $age_min ?: ''; ?>" min="18" max="50">
                            </div>
                            <div class="col-6">
                                <label for="age_max" class="form-label">年齢（最大）</label>
                                <input type="number" class="form-control" id="age_max" name="age_max" 
                                       value="<?php echo $age_max ?: ''; ?>" min="18" max="50">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">血液型</label>
                            <select class="form-select" id="blood_type" name="blood_type">
                                <option value="">すべて</option>
                                <option value="A" <?php echo ($blood_type == 'A') ? 'selected' : ''; ?>>A型</option>
                                <option value="B" <?php echo ($blood_type == 'B') ? 'selected' : ''; ?>>B型</option>
                                <option value="O" <?php echo ($blood_type == 'O') ? 'selected' : ''; ?>>O型</option>
                                <option value="AB" <?php echo ($blood_type == 'AB') ? 'selected' : ''; ?>>AB型</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>検索
                            </button>
                            <a href="?page=cast" class="btn btn-outline-secondary">
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
                    <h2 class="mb-1">キャスト一覧</h2>
                    <p class="text-muted mb-0">
                        <?php echo number_format($total_casts); ?>人のキャストが見つかりました
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0">並び順:</label>
                    <select class="form-select" id="sort" style="width: auto;" onchange="sortResults(this.value)">
                        <option value="created_at" <?php echo ($sort == 'created_at') ? 'selected' : ''; ?>>新着順</option>
                        <option value="age" <?php echo ($sort == 'age') ? 'selected' : ''; ?>>年齢順</option>
                        <option value="height" <?php echo ($sort == 'height') ? 'selected' : ''; ?>>身長順</option>
                        <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>名前順</option>
                    </select>
                </div>
            </div>
            
            <!-- キャスト一覧 -->
            <?php if (empty($casts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">該当するキャストが見つかりませんでした</h4>
                    <p class="text-muted">検索条件を変更して再度お試しください。</p>
                    <a href="?page=cast" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>条件をリセット
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($casts as $cast): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <?php if ($cast['profile_image']): ?>
                                    <img src="<?php echo htmlspecialchars($cast['profile_image']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($cast['name']); ?>"
                                         style="height: 250px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 250px;">
                                        <i class="fas fa-user fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="?page=cast_detail&id=<?php echo $cast['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($cast['name']); ?>
                                            </a>
                                        </h5>
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($cast['concept_type']); ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($cast['nickname']): ?>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo htmlspecialchars($cast['nickname']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-store me-1"></i>
                                        <?php echo htmlspecialchars($cast['shop_name']); ?>
                                    </p>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($cast['prefecture_name'] . $cast['city_name']); ?>
                                    </p>
                                    
                                    <div class="row small mb-2">
                                        <?php if ($cast['age']): ?>
                                        <div class="col-6">
                                            <i class="fas fa-birthday-cake me-1"></i>
                                            <?php echo $cast['age']; ?>歳
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($cast['height']): ?>
                                        <div class="col-6">
                                            <i class="fas fa-ruler-vertical me-1"></i>
                                            <?php echo $cast['height']; ?>cm
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($cast['blood_type']): ?>
                                        <div class="col-6">
                                            <i class="fas fa-heart me-1"></i>
                                            <?php echo $cast['blood_type']; ?>型
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($cast['hobby']): ?>
                                        <p class="card-text small mb-2">
                                            <strong>趣味:</strong> <?php echo htmlspecialchars($cast['hobby']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($cast['special_skill']): ?>
                                        <p class="card-text small mb-2">
                                            <strong>特技:</strong> <?php echo htmlspecialchars($cast['special_skill']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex gap-2">
                                            <a href="?page=cast_detail&id=<?php echo $cast['id']; ?>" 
                                               class="btn btn-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>詳細を見る
                                            </a>
                                            <?php if (is_logged_in()): ?>
                                                <button class="btn btn-outline-danger btn-sm favorite-btn" 
                                                        data-item-id="<?php echo $cast['id']; ?>" 
                                                        data-item-type="cast">
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
                    <nav aria-label="キャスト検索結果のページネーション">
                        <ul class="pagination">
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $pagination['prev_page']])); ?>">
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
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $pagination['next_page']])); ?>">
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
