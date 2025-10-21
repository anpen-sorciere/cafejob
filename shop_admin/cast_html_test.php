<?php
// エラー表示を有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ファイル読み込み
require_once 'includes.php';

// 認証チェック
if (!is_shop_admin()) {
    echo "認証エラー: 店舗管理者として認証されていません";
    exit;
}

// 変数設定
$page_title = 'キャスト管理';
$shop_id = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];

echo "<h1>HTML出力テスト</h1>";
echo "<p>page_title: " . htmlspecialchars($page_title) . "</p>";
echo "<p>shop_name: " . htmlspecialchars($shop_name) . "</p>";
echo "<p>shop_id: " . htmlspecialchars($shop_id) . "</p>";

// HTMLの出力テスト
ob_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo htmlspecialchars($shop_name); ?></title>
</head>
<body>
    <h1><?php echo $page_title; ?></h1>
    <p>店舗名: <?php echo htmlspecialchars($shop_name); ?></p>
</body>
</html>
<?php
$html_output = ob_get_clean();

echo "<h2>HTML出力成功</h2>";
echo "<pre>" . htmlspecialchars($html_output) . "</pre>";
?>
