<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes.php';

$page_title = '応募管理';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// フィルタリング用のパラメータ
$status_filter = $_GET['status'] ?? 'all';
$job_filter = $_GET['job_id'] ?? 'all';
$search = $_GET['search'] ?? '';

// 応募一覧の取得（フィルタリング対応）
$where_conditions = ["j.shop_id = ?"];
$params = [$shop_id];

if ($status_filter !== 'all') {
    $where_conditions[] = "a.status = ?";
    $params[] = $status_filter;
}

if ($job_filter !== 'all') {
    $where_conditions[] = "a.job_id = ?";
    $params[] = $job_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(u.username LIKE ? OR u.email LIKE ? OR j.title LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(' AND ', $where_conditions);

$applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, j.id as job_id,
            u.username, u.email, u.phone, u.first_name, u.last_name,
            u.created_at as user_created_at
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN users u ON a.user_id = u.id
     WHERE {$where_clause}
     ORDER BY a.applied_at DESC",
    $params
);

// 各応募の未読メッセージ数を取得
$unread_counts = get_unread_messages_by_application(null, 'shop_admin');

// 店舗の求人一覧（フィルター用）
$shop_jobs = $db->fetchAll(
    "SELECT id, title FROM jobs WHERE shop_id = ? ORDER BY title",
    [$shop_id]
);

// 応募ステータス更新処理
if ($_POST && isset($_POST['update_status'])) {
    $application_id = (int)$_POST['application_id'];
    $new_status = sanitize_input($_POST['status']);
    $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');
    
    // 自分の店舗の応募かチェック
    $application = $db->fetch(
        "SELECT a.id FROM applications a 
         JOIN jobs j ON a.job_id = j.id 
         WHERE a.id = ? AND j.shop_id = ?",
        [$application_id, $shop_id]
    );
    
    if ($application && in_array($new_status, ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])) {
        $db->query(
            "UPDATE applications SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?",
            [$new_status, $admin_notes, $application_id]
        );
        $_SESSION['success_message'] = '応募ステータスを更新しました。';
        header('Location: applications.php?' . http_build_query($_GET));
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
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($shop_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .application-item {
        transition: all 0.3s ease;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    
    .application-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .info-item {
        transition: all 0.3s ease;
        padding: 10px;
        border-radius: 8px;
    }
    
    .info-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .badge {
        border-radius: 20px;
        padding: 8px 12px;
        font-weight: 500;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
    }
    
    .badge.bg-info {
        background-color: #17a2b8 !important;
    }
    
    .badge.bg-primary {
        background-color: #007bff !important;
    }
    
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    /* モーダルの完全制御 */
    .modal {
        display: none !important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        overflow: hidden;
        outline: 0;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9998;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem auto;
        max-width: 500px;
        pointer-events: none;
    }
    
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 0.3rem;
        outline: 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem 1rem;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: calc(0.3rem - 1px);
        border-top-right-radius: calc(0.3rem - 1px);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
    }
    
    .modal-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        padding: 0.75rem;
        border-top: 1px solid #dee2e6;
        border-bottom-right-radius: calc(0.3rem - 1px);
        border-bottom-left-radius: calc(0.3rem - 1px);
    }
    
    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: white;
        opacity: 0.8;
    }
    
    .btn-close:hover {
        opacity: 1;
    }
    </style>
</head>
<body>
    <!-- ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-coffee me-2"></i>カフェJob
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="applications.php">
                    <i class="fas fa-file-alt me-1"></i>応募管理
                </a>
                <a class="nav-link" href="chat.php">
                    <i class="fas fa-comments me-1"></i>チャット
                    <?php 
                    $total_unread = get_unread_message_count(null, 'shop_admin');
                    if ($total_unread > 0): 
                    ?>
                        <span class="badge bg-danger ms-1"><?php echo $total_unread; ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-alt me-2"></i>応募管理
                    </h1>
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        総応募数: <span class="fw-bold"><?php echo count($applications); ?></span>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- フィルター -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">ステータス</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>すべて</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>未読</option>
                                    <option value="reviewed" <?php echo $status_filter === 'reviewed' ? 'selected' : ''; ?>>読了</option>
                                    <option value="interview" <?php echo $status_filter === 'interview' ? 'selected' : ''; ?>>面接</option>
                                    <option value="accepted" <?php echo $status_filter === 'accepted' ? 'selected' : ''; ?>>採用</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>不採用</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="job_id" class="form-label">求人</label>
                                <select class="form-select" id="job_id" name="job_id">
                                    <option value="all" <?php echo $job_filter === 'all' ? 'selected' : ''; ?>>すべて</option>
                                    <?php foreach ($shop_jobs as $job): ?>
                                        <option value="<?php echo $job['id']; ?>" <?php echo $job_filter == $job['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">検索</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="ユーザー名、メール、求人タイトルで検索" 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>検索
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (empty($applications)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">応募がありません</h5>
                            <p class="text-muted">条件に一致する応募が見つかりませんでした。</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- もえなび！スタイルの応募一覧 -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>応募一覧
                                <span class="badge bg-light text-primary ms-2"><?php echo count($applications); ?>件</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($applications as $index => $application): ?>
                                <div class="border-bottom p-3 application-item" data-application-id="<?php echo $application['id']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="me-3">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">
                                                        <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?>
                                                        <span class="badge bg-<?php 
                                                            echo $application['status'] === 'pending' ? 'warning' : 
                                                                ($application['status'] === 'reviewed' ? 'info' : 
                                                                    ($application['status'] === 'interview' ? 'primary' : 
                                                                        ($application['status'] === 'accepted' ? 'success' : 'danger'))); 
                                                        ?> ms-2">
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
                                                    </h6>
                                                    <p class="mb-1 text-muted small">
                                                        <i class="fas fa-briefcase me-1"></i>
                                                        <?php echo htmlspecialchars($application['job_title']); ?>
                                                    </p>
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        <?php echo htmlspecialchars($application['email']); ?>
                                                        <?php if (!empty($application['phone'])): ?>
                                                            <span class="ms-3">
                                                                <i class="fas fa-phone me-1"></i>
                                                                <?php echo htmlspecialchars($application['phone']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if (!empty($application['message'])): ?>
                                                        <div class="mt-2">
                                                            <p class="mb-0 small text-truncate" style="max-width: 500px;">
                                                                <i class="fas fa-comment me-1"></i>
                                                                <?php echo htmlspecialchars($application['message']); ?>
                                                            </p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('Y年m月d日 H:i', strtotime($application['applied_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        onclick="openStatusModal(<?php echo $application['id']; ?>)">
                                                    <i class="fas fa-edit me-1"></i>ステータス更新
                                                </button>
                                                <a href="application_detail.php?id=<?php echo $application['id']; ?>" 
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye me-1"></i>詳細
                                                </a>
                                                <a href="chat_detail.php?application_id=<?php echo $application['id']; ?>" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-comments me-1"></i>チャット
                                                    <?php if (isset($unread_counts[$application['id']]) && $unread_counts[$application['id']] > 0): ?>
                                                        <span class="badge bg-danger ms-1"><?php echo $unread_counts[$application['id']]; ?></span>
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ステータス更新モーダル -->
    <div id="statusModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">応募ステータス更新</h5>
                    <button type="button" class="btn-close" onclick="closeStatusModal()"></button>
                </div>
                <form method="POST" id="statusForm">
                    <div class="modal-body">
                        <input type="hidden" name="application_id" id="modalApplicationId">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">ステータス</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">未読</option>
                                <option value="reviewed">読了</option>
                                <option value="interview">面接</option>
                                <option value="accepted">採用</option>
                                <option value="rejected">不採用</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">管理者メモ</label>
                            <textarea class="form-control" id="admin_notes" 
                                      name="admin_notes" rows="3" 
                                      placeholder="面接日時、採用理由、不採用理由などを記録してください"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">キャンセル</button>
                        <button type="submit" name="update_status" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>更新
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // モーダル制御関数
    function openStatusModal(applicationId) {
        document.getElementById('modalApplicationId').value = applicationId;
        document.getElementById('statusModal').style.display = 'block';
        document.getElementById('statusModal').classList.add('show');
        
        // バックドロップを追加
        if (!document.querySelector('.modal-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop';
            document.body.appendChild(backdrop);
        }
        
        // フォーカスを最初の入力フィールドに
        setTimeout(function() {
            document.getElementById('status').focus();
        }, 100);
    }
    
    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
        document.getElementById('statusModal').classList.remove('show');
        
        // バックドロップを削除
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        
        // フォームをリセット
        document.getElementById('statusForm').reset();
    }
    
    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeStatusModal();
        }
    });
    
    // バックドロップクリックでモーダルを閉じる
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            closeStatusModal();
        }
    });
    
    // フォーム送信時の処理
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>処理中...';
        }
    });
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>