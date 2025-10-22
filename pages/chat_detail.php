<?php
/**
 * チャット詳細ページ
 * 特定のチャットルームでのメッセージのやり取り
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
$room_id = $_GET['room_id'] ?? null;
$db = new Database();

if (!$room_id) {
    header('Location: ?page=chat');
    exit;
}

// チャットルームの存在確認と権限チェック
$room = $db->fetch("
    SELECT 
        cr.*,
        s.name as shop_name,
        j.title as job_title,
        a.status as application_status
    FROM chat_rooms cr
    JOIN shops s ON cr.shop_id = s.id
    JOIN applications a ON cr.application_id = a.id
    JOIN jobs j ON a.job_id = j.id
    WHERE cr.id = ? AND cr.user_id = ?
", [$room_id, $user_id]);

if (!$room) {
    $_SESSION['error_message'] = 'チャットルームが見つかりません。';
    header('Location: ?page=chat');
    exit;
}

// メッセージ送信処理
if ($_POST['action'] ?? '' === 'send_message') {
    $message = trim($_POST['message'] ?? '');
    $message_type = 'text';
    $file_path = null;
    
    // ファイルアップロード処理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/chat/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['image']['name']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array(strtolower($file_info['extension']), $allowed_extensions) && 
            $_FILES['image']['size'] <= $max_file_size) {
            
            $file_name = uniqid() . '_' . time() . '.' . $file_info['extension'];
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $message_type = 'image';
                if (empty($message)) {
                    $message = '[画像を送信しました]';
                }
            } else {
                $_SESSION['error_message'] = 'ファイルのアップロードに失敗しました。';
            }
        } else {
            $_SESSION['error_message'] = '無効なファイル形式またはファイルサイズが大きすぎます。';
        }
    }
    
    if (!empty($message)) {
        try {
            // メッセージを送信
            $db->query("
                INSERT INTO chat_messages (room_id, sender_type, sender_id, message, message_type, file_path, created_at)
                VALUES (?, 'user', ?, ?, ?, ?, NOW())
            ", [$room_id, $user_id, $message, $message_type, $file_path]);
            
            // ルームの更新時間を更新
            $db->query("
                UPDATE chat_rooms SET updated_at = NOW() WHERE id = ?
            ", [$room_id]);
            
            // 店舗管理者に通知
            $db->query("
                INSERT INTO chat_notifications (room_id, recipient_type, recipient_id, message_id, created_at)
                SELECT ?, 'shop_admin', sa.id, LAST_INSERT_ID(), NOW()
                FROM shop_admins sa
                WHERE sa.shop_id = ?
            ", [$room_id, $room['shop_id']]);
            
            $_SESSION['success_message'] = 'メッセージを送信しました。';
            header('Location: ?page=chat_detail&room_id=' . $room_id);
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
    WHERE room_id = ? AND sender_type = 'shop_admin' AND is_read = FALSE
", [$room_id]);

$page_title = 'チャット - ' . $room['shop_name'];
ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- ヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-comments me-2"></i>チャット
                    </h1>
                    <p class="text-muted mb-0">
                        <?php echo htmlspecialchars($room['shop_name']); ?> - <?php echo htmlspecialchars($room['job_title']); ?>
                    </p>
                </div>
                <div>
                    <a href="?page=chat" class="btn btn-outline-secondary">
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
                            <div class="message-item mb-3 <?php echo $message['sender_type'] === 'user' ? 'text-end' : 'text-start'; ?>">
                                <div class="d-inline-block">
                                    <div class="message-bubble <?php echo $message['sender_type'] === 'user' ? 'bg-primary text-white' : 'bg-light'; ?>" style="max-width: 70%; padding: 10px 15px; border-radius: 18px;">
                                        <?php if ($message['message_type'] === 'image' && $message['file_path']): ?>
                                            <div class="message-image mb-2">
                                                <img src="<?php echo htmlspecialchars($message['file_path']); ?>" 
                                                     alt="送信された画像" 
                                                     class="img-fluid rounded" 
                                                     style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                                     onclick="openImageModal('<?php echo htmlspecialchars($message['file_path']); ?>')">
                                            </div>
                                        <?php endif; ?>
                                        <div class="message-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                        <div class="message-meta mt-1" style="font-size: 0.8em; opacity: 0.7;">
                                            <?php echo $message['sender_type'] === 'user' ? $message['user_name'] : $message['shop_admin_name']; ?>
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
                    <form method="POST" id="message-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="send_message">
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" name="image" id="image-upload" accept="image/*">
                            <small class="text-muted">画像ファイル（JPG, PNG, GIF）最大5MB</small>
                        </div>
                        <div class="input-group">
                            <textarea class="form-control" name="message" placeholder="メッセージを入力してください..." rows="2"></textarea>
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

<!-- 画像拡大表示モーダル -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">画像を表示</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="拡大画像" class="img-fluid">
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

.message-image img {
    transition: transform 0.2s;
}

.message-image img:hover {
    transform: scale(1.05);
}
</style>

<script>
// チャットエリアを最下部にスクロール
function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// 画像拡大表示
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// ページ読み込み時に最下部にスクロール
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// メッセージ送信時の処理
document.getElementById('message-form').addEventListener('submit', function(e) {
    const messageTextarea = this.querySelector('textarea[name="message"]');
    const imageUpload = this.querySelector('input[name="image"]');
    const message = messageTextarea.value.trim();
    const hasImage = imageUpload.files.length > 0;
    
    if (message === '' && !hasImage) {
        e.preventDefault();
        alert('メッセージまたは画像を入力してください。');
        return;
    }
    
    // ファイルサイズチェック
    if (hasImage) {
        const file = imageUpload.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            e.preventDefault();
            alert('ファイルサイズが大きすぎます。5MB以下の画像を選択してください。');
            return;
        }
    }
    
    // 送信ボタンを無効化
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
});

// ファイル選択時のプレビュー
document.getElementById('image-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // プレビュー表示（オプション）
            console.log('画像が選択されました:', file.name);
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
