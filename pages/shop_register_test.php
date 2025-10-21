<?php
$page_title = '店舗登録テスト';
$page_description = '店舗登録のテストを行います。';

// シンプルなテスト
if ($_POST && isset($_POST['test'])) {
    echo "<h1>テスト結果</h1>";
    echo "<p>POSTデータが送信されました！</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "<h1>店舗登録テスト</h1>";
    echo "<p>フォーム送信のテストを行います。</p>";
    
    ob_start();
    ?>
    
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0">フォーム送信テスト</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="test_name" class="form-label">テスト名</label>
                                <input type="text" class="form-control" id="test_name" name="test_name" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="test" class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i>テスト送信
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    $content = ob_get_clean();
    include 'includes/layout.php';
}
?>
