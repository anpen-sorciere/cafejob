<?php
/**
 * ロック機能テスト用スクリプト
 * 住所変更をロック状態にするテスト
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// 店舗管理者認証チェック
if (!is_shop_admin()) {
    header('Location: ../?page=shop_admin_login');
    exit;
}

$db = new Database();
$shop_id = $_SESSION['shop_id'];

// テスト用の住所変更レコードを作成（ロック状態）
if ($_POST['action'] === 'create_lock') {
    $db->query(
        "INSERT INTO shop_address_changes 
         (shop_id, old_postal_code, old_prefecture_id, old_city_name, old_address,
          new_postal_code, new_prefecture_id, new_city_name, new_address, 
          status, verification_code, failed_attempts, is_locked, locked_at, verification_sent_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', '123456', 3, TRUE, NOW(), NOW())",
        [$shop_id, '1234567', 1, 'テスト市', 'テスト住所', 
         '7654321', 2, '新テスト市', '新テスト住所']
    );
    
    $_SESSION['success_message'] = 'ロック状態を作成しました。';
    header('Location: test_lock.php');
    exit;
}

// ロック状態を解除
if ($_POST['action'] === 'unlock') {
    $db->query(
        "UPDATE shop_address_changes SET is_locked = FALSE, locked_at = NULL WHERE shop_id = ? AND is_locked = TRUE",
        [$shop_id]
    );
    
    $_SESSION['success_message'] = 'ロック状態を解除しました。';
    header('Location: test_lock.php');
    exit;
}

// 現在のロック状態を確認
$locked_address_change = $db->fetch(
    "SELECT * FROM shop_address_changes 
     WHERE shop_id = ? AND status = 'pending' AND is_locked = TRUE 
     ORDER BY created_at DESC LIMIT 1",
    [$shop_id]
);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ロック機能テスト</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1>ロック機能テスト</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5>現在のロック状態</h5>
            </div>
            <div class="card-body">
                <?php if ($locked_address_change): ?>
                    <div class="alert alert-danger">
                        <strong>ロック中</strong><br>
                        ID: <?php echo $locked_address_change['id']; ?><br>
                        失敗回数: <?php echo $locked_address_change['failed_attempts']; ?><br>
                        ロック日時: <?php echo $locked_address_change['locked_at']; ?>
                    </div>
                    
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="unlock">
                        <button type="submit" class="btn btn-success">ロック解除</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success">
                        ロックされていません
                    </div>
                    
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="create_lock">
                        <button type="submit" class="btn btn-danger">ロック状態を作成</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-primary">ダッシュボードに戻る</a>
        </div>
    </div>
</body>
</html>
