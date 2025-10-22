<?php
/**
 * 店舗管理者側チャット詳細ページ
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
$room_id = $_GET['room_id'] ?? null;
$application_id = $_GET['application_id'] ?? null;

// データベース接続を確実に取得
$db = new Database();

// デバッグ用：リクエストパラメータをログに記録
error_log("Chat detail request - room_id: $room_id, application_id: $application_id, shop_id: $shop_id");

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
            error_log("Application not found for chat - application_id: $application_id, shop_id: $shop_id");
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
            error_log("Using existing chat room: " . json_encode($room));
        } else {
            // チャットルームを作成
            error_log("Creating new chat room for application_id: $application_id");
            $stmt = $db->query("
                INSERT INTO chat_rooms (shop_id, user_id, application_id, created_at, updated_at) 
                VALUES (?, ?, ?, NOW(), NOW())
            ", [$shop_id, $application['user_id'], $application_id]);
            
            $room_id = $db->lastInsertId();
            error_log("Created chat room with id: $room_id");
            
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
            
            if (!$room) {
                error_log("Failed to retrieve created chat room");
                throw new Exception("チャットルームの作成後に情報の取得に失敗しました");
            }
        }
    } catch (Exception $e) {
        error_log("Chat room auto-creation error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $_SESSION['error_message'] = 'チャットルームの作成に失敗しました: ' . $e->getMessage();
        header('Location: applications.php');
        exit;
    }
} else if ($room_id) {
    // room_idが指定されている場合（チャット一覧からアクセス）
    try {
        error_log("Chat detail - room_id: $room_id, shop_id: $shop_id");
        
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
            error_log("Chat room not found - room_id: $room_id, shop_id: $shop_id");
            $_SESSION['error_message'] = 'チャットルームが見つかりません。';
            header('Location: chat.php');
            exit;
        }
        
        error_log("Chat room found: " . json_encode($room));
        
    } catch (Exception $e) {
        error_log("Chat room query error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $_SESSION['error_message'] = 'チャットルームの取得に失敗しました: ' . $e->getMessage();
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
    
    // 画像アップロード処理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/chat_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $file_name = uniqid() . '.' . $file_extension;
            $file_path = 'uploads/chat_images/' . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $file_path)) {
                $message_type = 'image';
            } else {
                $_SESSION['error_message'] = '画像のアップロードに失敗しました。';
            }
        } else {
            $_SESSION['error_message'] = '対応していない画像形式です。';
        }
    }
    
    if (!empty($message) || $message_type === 'image') {
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
            
            $_SESSION['success_message'] = 'メッセージを送信しました。';
            
            // リダイレクト先を決定
            $redirect_url = 'chat_detail.php?room_id=' . $room['id'];
            if ($application_id) {
                $redirect_url .= '&application_id=' . $application_id;
            }
            header('Location: ' . $redirect_url);
            exit;
            
        } catch (Exception $e) {
            error_log("Message send error: " . $e->getMessage());
            $_SESSION['error_message'] = 'メッセージの送信に失敗しました: ' . $e->getMessage();
        }
    }
}

// メッセージ一覧を取得
try {
    error_log("Fetching messages for room_id: " . $room['id']);
    
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
    
    error_log("Found " . count($messages) . " messages");
    
} catch (Exception $e) {
    error_log("Messages query error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    $messages = [];
    $_SESSION['error_message'] = 'メッセージの取得に失敗しました: ' . $e->getMessage();
}

// 未読メッセージを既読にマーク
try {
    $db->query("
        UPDATE chat_messages 
        SET is_read = TRUE, read_at = NOW() 
        WHERE room_id = ? AND sender_type = 'user' AND is_read = FALSE
    ", [$room['id']]);
} catch (Exception $e) {
    error_log("Mark messages as read error: " . $e->getMessage());
}

$page_title = 'チャット - ' . $room['last_name'] . ' ' . $room['first_name'];
ob_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .message-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
    }
    
    .message-item {
        margin-bottom: 1rem;
    }
    
    .message-image img {
        max-width: 200px;
        max-height: 200px;
        cursor: pointer;
    }
    
    .message-meta {
        font-size: 0.75em;
        opacity: 0.8;
    }
    
    .chat-container {
        height: 500px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }
    
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
    }
    
    .image-modal img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
    }
    
    .image-modal .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 30px;
        cursor: pointer;
    }
    </style>
</head>
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

                <!-- 成功メッセージ -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
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
                        <a href="applications.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-file-alt me-1"></i>応募管理に戻る
                        </a>
                    </div>
                </div>

                <!-- チャットエリア -->
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
                    
                    <div class="card-body p-0">
                        <!-- メッセージ表示エリア -->
                        <div class="chat-container" id="chatContainer">
                            <?php if (empty($messages)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>まだメッセージがありません。</p>
                                    <p>メッセージを送信して会話を始めましょう。</p>
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
                                            
                                            <div class="message-bubble <?php echo $message['sender_type'] === 'shop_admin' ? 'bg-primary text-white' : 'bg-light border'; ?>">
                                               
                                               <?php if ($message['message_type'] === 'image' && $message['file_path']): ?>
                                                   <div class="message-image mb-2">
                                                       <img src="<?php echo htmlspecialchars($message['file_path']); ?>" 
                                                            alt="送信された画像" 
                                                            class="img-fluid rounded" 
                                                            onclick="openImageModal('<?php echo htmlspecialchars($message['file_path']); ?>')">
                                                   </div>
                                               <?php endif; ?>
                                               
                                               <div class="message-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                               
                                               <div class="message-meta mt-2">
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
                   </div>
                   
                   <!-- メッセージ送信フォーム -->
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
   <div id="imageModal" class="image-modal">
       <span class="close" onclick="closeImageModal()">&times;</span>
       <img id="modalImage" src="" alt="拡大画像">
   </div>

   <script>
   // 画像プレビュー機能
   document.getElementById('image-upload').addEventListener('change', function(e) {
       const file = e.target.files[0];
       if (file) {
           const reader = new FileReader();
           reader.onload = function(e) {
               document.getElementById('preview-img').src = e.target.result;
               document.getElementById('image-preview').style.display = 'block';
           };
           reader.readAsDataURL(file);
       }
   });

   function clearImagePreview() {
       document.getElementById('image-upload').value = '';
       document.getElementById('image-preview').style.display = 'none';
   }

   // 画像拡大表示
   function openImageModal(imageSrc) {
       document.getElementById('modalImage').src = imageSrc;
       document.getElementById('imageModal').style.display = 'block';
   }

   function closeImageModal() {
       document.getElementById('imageModal').style.display = 'none';
   }

   // モーダル外クリックで閉じる
   window.onclick = function(event) {
       const modal = document.getElementById('imageModal');
       if (event.target === modal) {
           modal.style.display = 'none';
       }
   }

   // クイックメッセージ送信
   function sendQuickMessage(message) {
       document.querySelector('textarea[name="message"]').value = message;
       document.getElementById('message-form').submit();
   }

   // チャットエリアを最下部にスクロール
   function scrollToBottom() {
       const chatContainer = document.getElementById('chatContainer');
       chatContainer.scrollTop = chatContainer.scrollHeight;
   }

   // ページ読み込み時に最下部にスクロール
   window.addEventListener('load', scrollToBottom);

   // フォーム送信時の処理
   document.getElementById('message-form').addEventListener('submit', function(e) {
       const message = document.querySelector('textarea[name="message"]').value.trim();
       const image = document.getElementById('image-upload').files[0];
       
       if (!message && !image) {
           e.preventDefault();
           alert('メッセージまたは画像を入力してください。');
           return;
       }
       
       const submitBtn = document.getElementById('send-btn');
       submitBtn.disabled = true;
       submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
   });
   </script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>