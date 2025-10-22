<?php
/**
 * 店舗管理者側チャット機能
 * 応募者とのチャットを管理
 */

// 既存セッションを完全に破棄してから新しいセッションを開始
if (session_status() !== PHP_SESSION_NONE) {
    session_destroy();
}

// セッション設定をリセット
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Lax');

// cafejob専用セッションを開始
session_name('cafejob_session');
session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

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

// 住所変更がロックされている場合は確認ページにリダイレクト
$db = new Database();
$shop_id = $_SESSION['shop_id'];

$locked_address_change = $db->fetch(
    "SELECT id FROM shop_address_changes 
     WHERE shop_id = ? AND status = 'pending' AND is_locked = TRUE 
     ORDER BY created_at DESC LIMIT 1",
    [$shop_id]
);

if ($locked_address_change && basename($_SERVER['PHP_SELF']) !== 'verify_address.php') {
    $_SESSION['address_verification_pending'] = true;
    header('Location: verify_address.php');
    exit;
}

$shop_id = $_SESSION['shop_id'];
$shop_admin_id = $_SESSION['shop_admin_id'];

// データベース接続を確実に取得
$db = new Database();

// チャットルーム一覧を取得（エラーハンドリング付き）
$chat_rooms = [];
try {
    // テーブルの存在確認
    $chat_rooms_exists = $db->fetch("SHOW TABLES LIKE 'chat_rooms'");
    $chat_messages_exists = $db->fetch("SHOW TABLES LIKE 'chat_messages'");
    
    if (!$chat_rooms_exists || !$chat_messages_exists) {
        // テーブルが存在しない場合は空配列を返す
        $chat_rooms = [];
        error_log("Chat tables do not exist - chat_rooms: " . ($chat_rooms_exists ? 'exists' : 'missing') . ", chat_messages: " . ($chat_messages_exists ? 'exists' : 'missing'));
    } else {
        $chat_rooms = $db->fetchAll("
            SELECT 
                cr.*,
                u.username as user_name,
                u.first_name,
                u.last_name,
                j.title as job_title,
                a.status as application_status,
                COALESCE((SELECT COUNT(*) FROM chat_messages cm WHERE cm.room_id = cr.id AND cm.sender_type = 'user' AND cm.is_read = FALSE), 0) as unread_count,
                (SELECT cm.message FROM chat_messages cm WHERE cm.room_id = cr.id ORDER BY cm.created_at DESC LIMIT 1) as last_message,
                (SELECT cm.created_at FROM chat_messages cm WHERE cm.room_id = cr.id ORDER BY cm.created_at DESC LIMIT 1) as last_message_time
            FROM chat_rooms cr
            JOIN users u ON cr.user_id = u.id
            JOIN applications a ON cr.application_id = a.id
            JOIN jobs j ON a.job_id = j.id
            WHERE cr.shop_id = ?
            ORDER BY cr.updated_at DESC
        ", [$shop_id]);
    }
} catch (Exception $e) {
    // エラーが発生した場合は空配列を返す
    $chat_rooms = [];
    error_log("Chat rooms query error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
}

$page_title = 'チャット管理';

// デバッグ情報をログに記録
error_log("Shop admin chat.php - shop_id: $shop_id, chat_rooms_count: " . count($chat_rooms));

ob_start();
?>

<body>
    <!-- ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-coffee me-2"></i>カフェJob
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="applications.php">
                    <i class="fas fa-file-alt me-1"></i>応募管理
                </a>
                <a class="nav-link active" href="chat.php">
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

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-comments me-2"></i>チャット管理
                    </h1>
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        応募者とのやり取りを管理できます
                    </div>
                </div>
            </div>
        </div>

    <?php if (empty($chat_rooms)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">チャットルームがありません</h5>
                        <p class="text-muted">応募があると、応募者とのチャットルームが作成されます。</p>
                        <a href="applications.php" class="btn btn-primary">
                            <i class="fas fa-file-alt me-1"></i>応募管理を確認
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>チャットルーム一覧
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($chat_rooms as $room): ?>
                            <div class="border-bottom p-3 chat-room-item" data-room-id="<?php echo $room['id']; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 me-2">
                                                <?php echo htmlspecialchars($room['last_name'] . ' ' . $room['first_name']); ?>
                                                <small class="text-muted">(@<?php echo htmlspecialchars($room['user_name']); ?>)</small>
                                            </h6>
                                            <span class="badge bg-<?php echo $room['application_status'] === 'accepted' ? 'success' : ($room['application_status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                                <?php echo $room['application_status'] === 'accepted' ? '採用' : ($room['application_status'] === 'pending' ? '審査中' : '不採用'); ?>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-briefcase me-1"></i>
                                            <?php echo htmlspecialchars($room['job_title']); ?>
                                        </p>
                                        <?php if ($room['last_message']): ?>
                                            <p class="mb-0 text-truncate" style="max-width: 400px;">
                                                <?php echo htmlspecialchars($room['last_message']); ?>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo time_ago($room['last_message_time']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <?php if ($room['unread_count'] > 0): ?>
                                            <span class="badge bg-danger rounded-pill"><?php echo $room['unread_count']; ?></span>
                                        <?php endif; ?>
                                        <a href="chat_detail.php?room_id=<?php echo $room['id']; ?>" class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="fas fa-comment me-1"></i>チャットを開く
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
</body>
</html>

<style>
.chat-room-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.chat-room-item:last-child {
    border-bottom: none !important;
}
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

