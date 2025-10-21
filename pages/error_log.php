<?php
$page_title = 'エラーログ表示';
$page_description = 'システムのエラーログを表示します。';

$log_file = __DIR__ . '/logs/custom_error.log';
$log_content = '';

if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    // 最新の50行のみ表示
    $lines = explode("\n", $log_content);
    $log_content = implode("\n", array_slice($lines, -50));
} else {
    $log_content = 'ログファイルが存在しません。';
}

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-bug me-2"></i>エラーログ表示
                    </h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="?page=error_log&action=clear" class="btn btn-warning">
                            <i class="fas fa-trash me-2"></i>ログをクリア
                        </a>
                        <a href="?page=error_log&action=refresh" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>更新
                        </a>
                    </div>
                    
                    <?php if (isset($_GET['action']) && $_GET['action'] === 'clear'): ?>
                        <?php
                        if (file_exists($log_file)) {
                            file_put_contents($log_file, '');
                            echo '<div class="alert alert-success">ログをクリアしました。</div>';
                        }
                        ?>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">ログファイルの場所</h6>
                        <code><?php echo htmlspecialchars($log_file); ?></code>
                    </div>
                    
                    <h5>最新のログエントリ（最新50行）</h5>
                    <pre class="bg-light p-3" style="max-height: 500px; overflow-y: auto;"><?php echo htmlspecialchars($log_content); ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
