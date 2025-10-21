<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes.php';

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

$page_title = 'キャスト管理';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

// キャスト一覧の取得
$casts = $db->fetchAll("SELECT * FROM casts WHERE shop_id = ?", [$shop_id]);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo htmlspecialchars($shop_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1><?php echo $page_title; ?></h1>
        <p>店舗名: <?php echo htmlspecialchars($shop_name); ?></p>
        
        <?php if (empty($casts)): ?>
            <div class="alert alert-info">
                <h4>まだキャストが登録されていません</h4>
                <p>店舗で働くスタッフの情報を登録して、求人に活用しましょう。</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($casts as $cast): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($cast['name']); ?></h5>
                                <p class="card-text">
                                    年齢: <?php echo $cast['age'] ? $cast['age'] . '歳' : '未設定'; ?><br>
                                    ステータス: <?php echo $cast['status'] === 'active' ? '在籍中' : '非アクティブ'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
