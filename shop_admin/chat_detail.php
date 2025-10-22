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
$application_id = $_GET['application_id'] ?? null;
$db = new Database();

// application_idが指定されている場合（応募管理画面から直接アクセス）
if ($application_id) {
    try {
        // 応募情報を取得
        $application = $db->fetch("
            SELECT a.id, a.user_id, j.title as job_title, a.status as application_status
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            WHERE a.id = ? AND j.shop_id = ?
        ", [$application_id, $shop_id]);
        
        if (!$application) {
            $_SESSION['error_message'] = '応募が見つかりません。';
            header('Location: applications.php');
            exit;
        }
        
        // 既存のチャットルームをチェック
        $existing_room = $db->fetch("
            SELECT cr.*, u.username as user_name, u.first_name, u.last_name
            FROM chat_rooms cr
            JOIN users u ON cr.user_id = u.id
            WHERE cr.application_id = ? AND cr.shop_id = ?
        ", [$application_id, $shop_id]);
        
        if ($existing_room) {
            // 既存のルームがある場合はそのルームを使用
            $room = $existing_room;
            $room['job_title'] = $application['job_title'];
            $room['application_status'] = $application['application_status'];
        } else {
            // チャットルームを作成
            $stmt = $db->query("
                INSERT INTO chat_rooms (shop_id, user_id, application_id, created_at, updated_at) 
                VALUES (?, ?, ?, NOW(), NOW())
            ", [$shop_id, $application['user_id'], $application_id]);
            
            $room_id = $db->lastInsertId();
            
            // 作成したルームの情報を取得
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
                WHERE cr.id = ?
            ", [$room_id]);
        }
    } catch (Exception $e) {
        error_log("Chat room auto-creation error: " . $e->getMessage());
        $_SESSION['error_message'] = 'チャットルームの作成に失敗しました。';
        header('Location: applications.php');
        exit;
    }
} else if ($room_id) {
    // room_idが指定されている場合（チャット一覧からアクセス）
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
} else {
    // どちらも指定されていない場合はチャット一覧にリダイレクト
    header('Location: chat.php');
    exit;
}

// メッセージ送信処理
if ($_POST['action'] ?? '' === 'send_message') {
    $message = trim($_POST['message'] ?? '');
    $message_type = 'text';
    $file_path = null;
    
    // ファイルアップロード処理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/chat/';
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
                // 画像内容検証（無料のローカル検証）
                if (ENABLE_IMAGE_VALIDATION) {
                    require_once '../includes/local_image_validator.php';
                    $validator = new LocalImageValidator();
                    
                    if (!$validator->isImageAppropriate($file_path, $_FILES['image']['name'])) {
                        // 不適切な画像の場合は削除
                        unlink($file_path);
                        $_SESSION['error_message'] = '不適切な画像が検出されました。適切な画像を送信してください。';
                        header('Location: chat_detail.php?room_id=' . $room_id);
                        exit;
                    }
                }
                
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
                VALUES (?, 'shop_admin', ?, ?, ?, ?, NOW())
            ", [$room['id'], $shop_admin_id, $message, $message_type, $file_path]);
            
            // ルームの更新時間を更新
            $db->query("
                UPDATE chat_rooms SET updated_at = NOW() WHERE id = ?
            ", [$room['id']]);
            
            // ユーザーに通知
            $db->query("
                INSERT INTO chat_notifications (room_id, recipient_type, recipient_id, message_id, created_at)
                VALUES (?, 'user', ?, LAST_INSERT_ID(), NOW())
            ", [$room['id'], $room['user_id']]);
            
            $_SESSION['success_message'] = 'メッセージを送信しました。';
            
            // リダイレクト先を決定
            $redirect_url = 'chat_detail.php?room_id=' . $room['id'];
            if ($application_id) {
                $redirect_url .= '&application_id=' . $application_id;
            }
            header('Location: ' . $redirect_url);
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
", [$room['id']]);

// 未読メッセージを既読にマーク
$db->query("
    UPDATE chat_messages 
    SET is_read = TRUE, read_at = NOW() 
    WHERE room_id = ? AND sender_type = 'user' AND is_read = FALSE
", [$room['id']]);

$page_title = 'チャット - ' . $room['last_name'] . ' ' . $room['first_name'];
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- エラーメッセージ -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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

            <!-- もえなび！スタイルのチャットエリア -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white">
                                    <i class="fas fa-comment-dots me-2"></i>メッセージ
                                </h5>
                                <small class="text-white-50">
                                    <?php echo htmlspecialchars($room['last_name'] . ' ' . $room['first_name']); ?> (@<?php echo htmlspecialchars($room['user_name']); ?>)
                                </small>
                            </div>
                        </div>
                        <span class="badge bg-light text-primary">
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
                                <div class="d-flex align-items-start <?php echo $message['sender_type'] === 'shop_admin' ? 'justify-content-end' : 'justify-content-start'; ?>">
                                    <?php if ($message['sender_type'] === 'user'): ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px; font-size: 0.8em;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="message-bubble <?php echo $message['sender_type'] === 'shop_admin' ? 'bg-primary text-white' : 'bg-light border'; ?>" 
                                         style="max-width: 70%; padding: 12px 16px; border-radius: 18px; position: relative;">
                                        
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
                                        
                                        <div class="message-meta mt-2" style="font-size: 0.75em; opacity: 0.8;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">
                                                    <?php echo $message['sender_type'] === 'shop_admin' ? $message['shop_admin_name'] : $message['user_name']; ?>
                                                </span>
                                                <span><?php echo date('m/d H:i', strtotime($message['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($message['sender_type'] === 'shop_admin'): ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center ms-2" 
                                             style="width: 32px; height: 32px; font-size: 0.8em;">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- もえなび！スタイルのメッセージ送信フォーム -->
                <div class="card-footer bg-light">
                    <form method="POST" id="message-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="send_message">
                        
                        <!-- 画像アップロード -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <label for="image-upload" class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="fas fa-image me-1"></i>画像を選択
                                </label>
                                <input type="file" class="form-control form-control-sm d-none" name="image" id="image-upload" accept="image/*">
                                <small class="text-muted">JPG, PNG, GIF（最大5MB）</small>
                            </div>
                            <div id="image-preview" class="mt-2" style="display: none;">
                                <img id="preview-img" src="" alt="プレビュー" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearImagePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- メッセージ入力 -->
                        <div class="input-group">
                            <textarea class="form-control" name="message" placeholder="メッセージを入力してください..." rows="2" style="resize: none;"></textarea>
                            <button class="btn btn-primary" type="submit" id="send-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        
                        <!-- 送信オプション -->
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                メッセージまたは画像を送信できます
                            </small>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success" onclick="sendQuickMessage('面接のご連絡をいたします。')">
                                    <i class="fas fa-calendar me-1"></i>面接連絡
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="sendQuickMessage('ありがとうございます。')">
                                    <i class="fas fa-thumbs-up me-1"></i>ありがとう
                                </button>
                            </div>
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
/* もえなび！スタイルのチャット */
.message-bubble {
    word-wrap: break-word;
    word-break: break-word;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#chat-messages {
    scroll-behavior: smooth;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.message-item:last-child {
    margin-bottom: 0 !important;
}

.message-image img {
    transition: transform 0.2s;
    border-radius: 8px;
}

.message-image img:hover {
    transform: scale(1.05);
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
}

/* アニメーション効果 */
.message-item {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .message-bubble {
        max-width: 85% !important;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* 送信ボタンのホバー効果 */
#send-btn:hover {
    transform: scale(1.05);
    transition: all 0.2s ease;
}

/* クイックメッセージボタン */
.btn-outline-success:hover,
.btn-outline-info:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
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

// 画像プレビュー機能
function showImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('image-preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

// 画像プレビューをクリア
function clearImagePreview() {
    document.getElementById('image-upload').value = '';
    document.getElementById('image-preview').style.display = 'none';
}

// クイックメッセージ送信
function sendQuickMessage(message) {
    const textarea = document.querySelector('textarea[name="message"]');
    textarea.value = message;
    textarea.focus();
}

// ページ読み込み時の処理
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // 画像アップロードの処理
    document.getElementById('image-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // ファイルサイズチェック
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('ファイルサイズが大きすぎます。5MB以下の画像を選択してください。');
                this.value = '';
                return;
            }
            
            // ファイル形式チェック
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('対応していないファイル形式です。JPG、PNG、GIF形式の画像を選択してください。');
                this.value = '';
                return;
            }
            
            showImagePreview(file);
        }
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
        
        // 送信ボタンを無効化
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 送信中...';
    });
    
    // Enterキーで送信（Shift+Enterで改行）
    document.querySelector('textarea[name="message"]').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('message-form').dispatchEvent(new Event('submit'));
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
