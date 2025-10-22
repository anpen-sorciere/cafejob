<?php
/**
 * 店舗管理者側チャット詳細ページ
 * 応募者とのメッセージのやり取り
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// 店舗管理者認証チェック
if (!is_shop_admin()) {
    header('Location: ../?page=shop_login');
    exit;
}

$shop_id = $_SESSION['shop_id'];
$shop_admin_id = $_SESSION['shop_admin_id'];
$room_id = $_GET['room_id'] ?? null;
$db = new Database();

if (!$room_id) {
    header('Location: chat.php');
    exit;
}

// チャットルームの存在確認と権限チェック
$room = $db->fetch("
    SELECT 
        cr.*,
        u.username as user_name,
        u.first_name,
        u.last_name,
        j.title as job_title,
        a.status as application_status
    FROM chat_rooms cr
    JOIN users u ON cr.user_id = u.id
    JOIN applications a ON cr.application_id = a.id
    JOIN jobs j ON a.job_id = j.id
    WHERE cr.id = ? AND cr.shop_id = ?
", [$room_id, $shop_id]);

if (!$room) {
    $_SESSION['error_message'] = 'チャットルームが見つかりません。';
    header('Location: chat.php');
    exit;
}

// メッセージ送信処理
if ($_POST['action'] ?? '' === 'send_message') {
    $message = trim($_POST['message'] ?? '');
    
    if (!empty($message)) {
        try {
            // メッセージを送信
            $db->query("
                INSERT INTO chat_messages (room_id, sender_type, sender_id, message, created_at)
                VALUES (?, 'shop_admin', ?, ?, NOW())
            ", [$room_id, $shop_admin_id, $message]);
            
            // ルームの更新時間を更新
            $db->query("
                UPDATE chat_rooms SET updated_at = NOW() WHERE id = ?
            ", [$room_id]);
            
            // ユーザーに通知
            $db->query("
                INSERT INTO chat_notifications (room_id, recipient_type, recipient_id, message_id, created_at)
                VALUES (?, 'user', ?, LAST_INSERT_ID(), NOW())
            ", [$room_id, $room['user_id']]);
            
            $_SESSION['success_message'] = 'メッセージを送信しました。';
            header('Location: chat_detail.php?room_id=' . $room_id);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'メッセージの送信に失敗しました。';
        }
    }
}

// メッセージ一覧を取得
$messages = $db->fetchAll("
    SELECT 
        cm.*,
        u.username as user_name,
        sa.username as shop_admin_name
    FROM chat_messages cm
    LEFT JOIN users u ON cm.sender_type = 'user' AND cm.sender_id = u.id
    LEFT JOIN shop_admins sa ON cm.sender_type = 'shop_admin' AND cm.sender_id = sa.id
    WHERE cm.room_id = ?
    ORDER BY cm.created_at ASC
", [$room_id]);

// 未読メッセージを既読にマーク
$db->query("
    UPDATE chat_messages 
    SET is_read = TRUE, read_at = NOW() 
    WHERE room_id = ? AND sender_type = 'user' AND is_read = FALSE
", [$room_id]);

$page_title = 'チャット - ' . $room['last_name'] . ' ' . $room['first_name'];
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- ヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-comments me-2"></i>チャット
                    </h1>
                    <p class="text-muted mb-0">
                        <?php echo htmlspecialchars($room['last_name'] . ' ' . $room['first_name']); ?> (@<?php echo htmlspecialchars($room['user_name']); ?>) - <?php echo htmlspecialchars($room['job_title']); ?>
                    </p>
                </div>
                <div>
                    <a href="chat.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>チャット一覧に戻る
                    </a>
                </div>
            </div>

            <!-- チャットエリア -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-comment-dots me-2"></i>メッセージ
                        </h5>
                        <span class="badge bg-<?php echo $room['application_status'] === 'accepted' ? 'success' : ($room['application_status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                            <?php echo $room['application_status'] === 'accepted' ? '採用' : ($room['application_status'] === 'pending' ? '審査中' : '不採用'); ?>
                        </span>
                    </div>
                </div>
                
                <!-- メッセージ表示エリア -->
                <div class="card-body" style="height: 500px; overflow-y: auto;" id="chat-messages">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-comment-slash fa-2x mb-3"></i>
                            <p>まだメッセージがありません。<br>最初のメッセージを送信してみましょう。</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-item mb-3 <?php echo $message['sender_type'] === 'shop_admin' ? 'text-end' : 'text-start'; ?>">
                                <div class="d-inline-block">
                                    <div class="message-bubble <?php echo $message['sender_type'] === 'shop_admin' ? 'bg-primary text-white' : 'bg-light'; ?>" style="max-width: 70%; padding: 10px 15px; border-radius: 18px;">
                                        <div class="message-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                        <div class="message-meta mt-1" style="font-size: 0.8em; opacity: 0.7;">
                                            <?php echo $message['sender_type'] === 'shop_admin' ? $message['shop_admin_name'] : $message['user_name']; ?>
                                            <span class="ms-2"><?php echo date('m/d H:i', strtotime($message['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- メッセージ送信フォーム -->
                <div class="card-footer">
                    <form method="POST" id="message-form">
                        <input type="hidden" name="action" value="send_message">
                        <div class="input-group">
                            <textarea class="form-control" name="message" placeholder="メッセージを入力してください..." rows="2" required></textarea>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-bubble {
    word-wrap: break-word;
    word-break: break-word;
}

#chat-messages {
    scroll-behavior: smooth;
}

.message-item:last-child {
    margin-bottom: 0 !important;
}
</style>

<script>
// チャットエリアを最下部にスクロール
function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// ページ読み込み時に最下部にスクロール
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// メッセージ送信時の処理
document.getElementById('message-form').addEventListener('submit', function(e) {
    const messageTextarea = this.querySelector('textarea[name="message"]');
    const message = messageTextarea.value.trim();
    
    if (message === '') {
        e.preventDefault();
        return;
    }
    
    // 送信ボタンを無効化
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
