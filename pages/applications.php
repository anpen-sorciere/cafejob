<?php
$page_title = '応募履歴';
$page_description = 'あなたの応募履歴を表示します。';

// ログイン必須
require_login();

// 応募履歴の取得
$applications = $db->fetchAll(
    "SELECT a.*, j.title as job_title, j.salary_min, j.salary_max, j.job_type,
            s.name as shop_name, s.concept_type, s.image_url,
            p.name as prefecture_name, c.name as city_name
     FROM applications a
     JOIN jobs j ON a.job_id = j.id
     JOIN shops s ON j.shop_id = s.id
     LEFT JOIN prefectures p ON s.prefecture_id = p.id
     LEFT JOIN cities c ON s.city_id = c.id
     WHERE a.user_id = ?
     ORDER BY a.applied_at DESC",
    [$_SESSION['user_id']]
);

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-file-alt me-2"></i>応募履歴
            </h1>
            
            <?php if (empty($applications)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">応募履歴がありません</h4>
                    <p class="text-muted">求人に応募すると、ここに履歴が表示されます。</p>
                    <a href="?page=jobs" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>求人を探す
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($applications as $application): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">
                                            <a href="?page=job_detail&id=<?php echo $application['job_id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($application['job_title']); ?>
                                            </a>
                                        </h5>
                                        <span class="badge bg-<?php 
                                            echo $application['status'] == 'pending' ? 'warning' : 
                                                ($application['status'] == 'accepted' ? 'success' : 'danger'); 
                                        ?>">
                                            <?php 
                                            $status_labels = [
                                                'pending' => '審査中',
                                                'accepted' => '採用',
                                                'rejected' => '不採用',
                                                'cancelled' => 'キャンセル'
                                            ];
                                            echo $status_labels[$application['status']] ?? $application['status'];
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-store me-1"></i>
                                        <?php echo htmlspecialchars($application['shop_name']); ?>
                                    </p>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($application['prefecture_name'] . $application['city_name']); ?>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <?php if ($application['salary_min']): ?>
                                                <span class="badge bg-primary">
                                                    <?php echo number_format($application['salary_min']); ?>円〜
                                                </span>
                                            <?php endif; ?>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($application['job_type']); ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo time_ago($application['applied_at']); ?>
                                        </small>
                                    </div>
                                    
                                    <?php if ($application['message']): ?>
                                        <div class="mb-3">
                                            <h6 class="small text-muted mb-1">応募メッセージ</h6>
                                            <p class="small bg-light p-2 rounded">
                                                <?php echo htmlspecialchars($application['message']); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="?page=job_detail&id=<?php echo $application['job_id']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>詳細を見る
                                        </a>
                                        <a href="?page=chat_detail&application_id=<?php echo $application['id']; ?>" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-comments me-1"></i>チャット
                                        </a>
                                        <?php if ($application['status'] == 'pending'): ?>
                                            <button class="btn btn-outline-danger btn-sm cancel-application" 
                                                    data-application-id="<?php echo $application['id']; ?>">
                                                <i class="fas fa-times me-1"></i>キャンセル
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
    </div>
</div>

<script>
// 応募キャンセル機能
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancel-application').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('この応募をキャンセルしますか？')) {
                const applicationId = this.dataset.applicationId;
                
                fetch('api/cancel_application.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        application_id: applicationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'エラーが発生しました。');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('エラーが発生しました。');
                });
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
