<?php
/**
 * 管理者用画像監視機能
 * アップロードされた画像の監視と管理
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// システム管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

$db = new Database();

// 画像監視データを取得
$images = $db->fetchAll("
    SELECT 
        cm.*,
        cr.id as room_id,
        u.username as user_name,
        s.name as shop_name,
        sa.username as shop_admin_name
    FROM chat_messages cm
    JOIN chat_rooms cr ON cm.room_id = cr.id
    LEFT JOIN users u ON cr.user_id = u.id
    LEFT JOIN shops s ON cr.shop_id = s.id
    LEFT JOIN shop_admins sa ON cr.sender_type = 'shop_admin' AND cm.sender_id = sa.id
    WHERE cm.message_type = 'image' AND cm.file_path IS NOT NULL
    ORDER BY cm.created_at DESC
    LIMIT 50
");

$page_title = '画像監視';
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-shield-alt me-2"></i>画像監視
                </h1>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    チャットで送信された画像を監視
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($images)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">送信された画像がありません</h5>
                        <p class="text-muted">チャットで画像が送信されると、ここに表示されます。</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($images as $image): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <?php echo $image['sender_type'] === 'user' ? $image['user_name'] : $image['shop_admin_name']; ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo date('m/d H:i', strtotime($image['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (file_exists($image['file_path'])): ?>
                                <div class="text-center mb-3">
                                    <img src="<?php echo htmlspecialchars($image['file_path']); ?>" 
                                         alt="送信された画像" 
                                         class="img-fluid rounded" 
                                         style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                         onclick="openImageModal('<?php echo htmlspecialchars($image['file_path']); ?>')">
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p>画像ファイルが見つかりません</p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="small text-muted">
                                <p class="mb-1">
                                    <strong>店舗:</strong> <?php echo htmlspecialchars($image['shop_name']); ?>
                                </p>
                                <p class="mb-1">
                                    <strong>送信者:</strong> 
                                    <?php echo $image['sender_type'] === 'user' ? 'ユーザー' : '店舗管理者'; ?>
                                </p>
                                <?php if ($image['message']): ?>
                                    <p class="mb-0">
                                        <strong>メッセージ:</strong> <?php echo htmlspecialchars($image['message']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-outline-primary btn-sm" 
                                        onclick="openImageModal('<?php echo htmlspecialchars($image['file_path']); ?>')">
                                    <i class="fas fa-search-plus me-1"></i>拡大
                                </button>
                                <?php if (file_exists($image['file_path'])): ?>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteImage(<?php echo $image['id']; ?>)">
                                        <i class="fas fa-trash me-1"></i>削除
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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

<script>
// 画像拡大表示
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// 画像削除
function deleteImage(messageId) {
    if (confirm('この画像を削除しますか？')) {
        // AJAXで画像削除処理
        fetch('delete_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message_id=' + messageId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('削除に失敗しました: ' + data.error);
            }
        })
        .catch(error => {
            alert('エラーが発生しました: ' + error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
