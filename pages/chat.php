<?php
/**
 * ユーザー側チャット機能
 * 応募した求人について店舗とチャットできる
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// ユーザー認証チェック
if (!is_logged_in()) {
    header('Location: ?page=login');
    exit;
}

$user_id = $_SESSION['user_id'];
$db = new Database();

// チャットルーム一覧を取得
$chat_rooms = $db->fetchAll("
    SELECT 
        cr.*,
        s.name as shop_name,
        j.title as job_title,
        a.status as application_status,
        (SELECT COUNT(*) FROM chat_messages cm WHERE cm.room_id = cr.id AND cm.sender_type = 'shop_admin' AND cm.is_read = FALSE) as unread_count,
        (SELECT cm.message FROM chat_messages cm WHERE cm.room_id = cr.id ORDER BY cm.created_at DESC LIMIT 1) as last_message,
        (SELECT cm.created_at FROM chat_messages cm WHERE cm.room_id = cr.id ORDER BY cm.created_at DESC LIMIT 1) as last_message_time
    FROM chat_rooms cr
    JOIN shops s ON cr.shop_id = s.id
    JOIN applications a ON cr.application_id = a.id
    JOIN jobs j ON a.job_id = j.id
    WHERE cr.user_id = ?
    ORDER BY cr.updated_at DESC
", [$user_id]);

$page_title = 'チャット';
ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-comments me-2"></i>チャット
                </h1>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    応募した求人について店舗とやり取りできます
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
                        <p class="text-muted">求人に応募すると、店舗とのチャットルームが作成されます。</p>
                        <a href="?page=jobs" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>求人を探す
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
                                            <h6 class="mb-0 me-2"><?php echo htmlspecialchars($room['shop_name']); ?></h6>
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
                                        <a href="?page=chat_detail&room_id=<?php echo $room['id']; ?>" class="btn btn-outline-primary btn-sm mt-2">
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
include 'includes/layout.php';
?>

