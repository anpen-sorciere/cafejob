<?php
// エラーハンドリングを追加
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
$page_title = '求人検索';

// 検索パラメータの取得
$keyword = isset($_GET['keyword']) ? sanitize_input($_GET['keyword']) : '';
$prefecture_id = isset($_GET['prefecture']) ? (int)$_GET['prefecture'] : null;
$concept_type = isset($_GET['concept_type']) ? sanitize_input($_GET['concept_type']) : null;
$salary_min = isset($_GET['salary_min']) ? (int)$_GET['salary_min'] : null;
$job_type = isset($_GET['job_type']) ? sanitize_input($_GET['job_type']) : null;
$gender_requirement = isset($_GET['gender_requirement']) ? sanitize_input($_GET['gender_requirement']) : null;
$sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'created_at';
$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

// 検索クエリの構築
$sql = "SELECT j.*, s.name as shop_name, s.address, s.concept_type, s.image_url,
               p.name as prefecture_name, c.name as city_name,
               COUNT(a.id) as application_count
        FROM jobs j
        JOIN shops s ON j.shop_id = s.id
        LEFT JOIN prefectures p ON s.prefecture_id = p.id
        LEFT JOIN cities c ON s.city_id = c.id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.status = 'active' AND s.status = 'active'";

$params = [];

if ($keyword) {
    $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR s.name LIKE ?)";
    $params[] = "%$keyword%";
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

if ($salary_min) {
    $sql .= " AND j.salary_min >= ?";
    $params[] = $salary_min;
}

if ($job_type) {
    $sql .= " AND j.job_type = ?";
    $params[] = $job_type;
}

if ($gender_requirement) {
    $sql .= " AND (j.gender_requirement = ? OR j.gender_requirement = 'any')";
    $params[] = $gender_requirement;
}

$sql .= " GROUP BY j.id";

// ソート処理
switch ($sort) {
    case 'salary':
        $sql .= " ORDER BY j.salary_min DESC, j.created_at DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY application_count DESC, j.created_at DESC";
        break;
    case 'deadline':
        $sql .= " ORDER BY j.application_deadline ASC, j.created_at DESC";
        break;
    default:
        $sql .= " ORDER BY j.created_at DESC";
        break;
}

// 総件数の取得（より安全な方法）
$count_sql = "SELECT COUNT(DISTINCT j.id) as total
        FROM jobs j
        JOIN shops s ON j.shop_id = s.id
        LEFT JOIN prefectures p ON s.prefecture_id = p.id
        LEFT JOIN cities c ON s.city_id = c.id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.status = 'active' AND s.status = 'active'";

$count_params = [];

if ($keyword) {
    $count_sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR s.name LIKE ?)";
    $count_params[] = "%$keyword%";
    $count_params[] = "%$keyword%";
    $count_params[] = "%$keyword%";
}

if ($prefecture_id) {
    $count_sql .= " AND s.prefecture_id = ?";
    $count_params[] = $prefecture_id;
}

if ($concept_type) {
    $count_sql .= " AND s.concept_type = ?";
    $count_params[] = $concept_type;
}

if ($salary_min) {
    $count_sql .= " AND j.salary_min >= ?";
    $count_params[] = $salary_min;
}

if ($job_type) {
    $count_sql .= " AND j.job_type = ?";
    $count_params[] = $job_type;
}

if ($gender_requirement) {
    $count_sql .= " AND (j.gender_requirement = ? OR j.gender_requirement = 'any')";
    $count_params[] = $gender_requirement;
}

$total_jobs = $db->fetch($count_sql, $count_params)['total'];

// データが0件の場合は空配列を返す
if ($total_jobs == 0) {
    $jobs = [];
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
    $pagination = paginate($total_jobs, $items_per_page, $page_num);
    $offset = $pagination['offset'];

    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $items_per_page;
    $params[] = $offset;

    $jobs = $db->fetchAll($sql, $params);
}

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
                        <input type="hidden" name="page" value="jobs">
                        
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                   value="<?php echo htmlspecialchars($keyword); ?>" 
                                   placeholder="店舗名、職種など">
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
                            <label for="salary_min" class="form-label">最低給与</label>
                            <select class="form-select" id="salary_min" name="salary_min">
                                <option value="">指定なし</option>
                                <option value="800" <?php echo ($salary_min == 800) ? 'selected' : ''; ?>>800円以上</option>
                                <option value="900" <?php echo ($salary_min == 900) ? 'selected' : ''; ?>>900円以上</option>
                                <option value="1000" <?php echo ($salary_min == 1000) ? 'selected' : ''; ?>>1,000円以上</option>
                                <option value="1200" <?php echo ($salary_min == 1200) ? 'selected' : ''; ?>>1,200円以上</option>
                                <option value="1500" <?php echo ($salary_min == 1500) ? 'selected' : ''; ?>>1,500円以上</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_type" class="form-label">雇用形態</label>
                            <select class="form-select" id="job_type" name="job_type">
                                <option value="">すべて</option>
                                <option value="part_time" <?php echo ($job_type == 'part_time') ? 'selected' : ''; ?>>アルバイト</option>
                                <option value="full_time" <?php echo ($job_type == 'full_time') ? 'selected' : ''; ?>>正社員</option>
                                <option value="contract" <?php echo ($job_type == 'contract') ? 'selected' : ''; ?>>契約社員</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gender_requirement" class="form-label">性別</label>
                            <select class="form-select" id="gender_requirement" name="gender_requirement">
                                <option value="">すべて</option>
                                <option value="male" <?php echo ($gender_requirement == 'male') ? 'selected' : ''; ?>>男性</option>
                                <option value="female" <?php echo ($gender_requirement == 'female') ? 'selected' : ''; ?>>女性</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>検索
                            </button>
                            <a href="?page=jobs" class="btn btn-outline-secondary">
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
                    <h2 class="mb-1">求人検索結果</h2>
                    <p class="text-muted mb-0">
                        <?php echo number_format($total_jobs); ?>件の求人が見つかりました
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0">並び順:</label>
                    <select class="form-select" id="sort" style="width: auto;" onchange="sortResults(this.value)">
                        <option value="created_at" <?php echo ($sort == 'created_at') ? 'selected' : ''; ?>>新着順</option>
                        <option value="salary" <?php echo ($sort == 'salary') ? 'selected' : ''; ?>>給与順</option>
                        <option value="popular" <?php echo ($sort == 'popular') ? 'selected' : ''; ?>>人気順</option>
                        <option value="deadline" <?php echo ($sort == 'deadline') ? 'selected' : ''; ?>>締切順</option>
                    </select>
                </div>
            </div>
            
            <!-- 求人一覧 -->
            <?php if (empty($jobs)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">該当する求人が見つかりませんでした</h4>
                    <p class="text-muted">検索条件を変更して再度お試しください。</p>
                    <a href="?page=jobs" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>条件をリセット
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($jobs as $job): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <?php if ($job['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($job['image_url']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($job['shop_name']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="?page=job_detail&id=<?php echo $job['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($job['title']); ?>
                                            </a>
                                        </h5>
                                        <span class="badge badge-concept">
                                            <?php echo htmlspecialchars($job['concept_type']); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-store me-1"></i>
                                        <?php echo htmlspecialchars($job['shop_name']); ?>
                                    </p>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($job['prefecture_name'] . $job['city_name']); ?>
                                    </p>
                                    
                                    <?php if ($job['description']): ?>
                                        <p class="card-text small">
                                            <?php echo htmlspecialchars(mb_substr($job['description'], 0, 100)); ?>...
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <?php if ($job['salary_min']): ?>
                                                <span class="badge badge-salary">
                                                    <?php echo number_format($job['salary_min']); ?>円〜
                                                    <?php if ($job['salary_max']): ?>
                                                        <?php echo number_format($job['salary_max']); ?>円
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                <?php echo time_ago($job['created_at']); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="?page=job_detail&id=<?php echo $job['id']; ?>" 
                                               class="btn btn-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>詳細を見る
                                            </a>
                                            <?php if (is_logged_in()): ?>
                                                <button class="btn btn-outline-danger btn-sm favorite-btn" 
                                                        data-item-id="<?php echo $job['id']; ?>" 
                                                        data-item-type="job">
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
                    <nav aria-label="求人検索結果のページネーション">
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

} catch (Exception $e) {
    echo "<div class='container py-4'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>エラーが発生しました</h4>";
    echo "<p><strong>メッセージ:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>ファイル:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>行:</strong> " . $e->getLine() . "</p>";
    echo "<details><summary>スタックトレース</summary><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></details>";
    echo "</div>";
    echo "</div>";
} catch (Error $e) {
    echo "<div class='container py-4'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>致命的エラーが発生しました</h4>";
    echo "<p><strong>メッセージ:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>ファイル:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>行:</strong> " . $e->getLine() . "</p>";
    echo "<details><summary>スタックトレース</summary><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre></details>";
    echo "</div>";
    echo "</div>";
}
?>

