<?php
// 住所確認ページ デバッグ版
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

echo "<h1>住所確認ページ デバッグ版</h1>";

try {
    echo "<h2>1. セッション開始</h2>";
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        echo "セッション開始: 成功<br>";
    } else {
        echo "セッション既に開始済み<br>";
    }
    
    echo "<h2>2. セッションデータ確認</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h2>3. ファイル読み込み</h2>";
    require_once '../config/config.php';
    echo "config.php読み込み: 成功<br>";
    
    require_once '../config/database.php';
    echo "database.php読み込み: 成功<br>";
    
    require_once '../includes/functions.php';
    echo "functions.php読み込み: 成功<br>";
    
    echo "<h2>4. 認証チェック</h2>";
    if (!is_shop_admin()) {
        echo "店舗管理者認証: 失敗<br>";
        echo '<a href="../?page=shop_admin_login">ログインページへ</a><br>';
        exit;
    } else {
        echo "店舗管理者認証: 成功<br>";
    }
    
    echo "<h2>5. 住所確認状態チェック</h2>";
    $shop_status = $_SESSION['shop_status'] ?? 'unknown';
    $address_verification_pending = isset($_SESSION['address_verification_pending']);
    
    echo "店舗ステータス: " . htmlspecialchars($shop_status) . "<br>";
    echo "住所確認待ち: " . ($address_verification_pending ? "はい" : "いいえ") . "<br>";
    
    if ($shop_status !== 'verification_pending' && !$address_verification_pending) {
        echo "住所確認が不要です。ダッシュボードにリダイレクトします。<br>";
        echo '<a href="dashboard.php">ダッシュボードへ</a><br>';
        exit;
    }
    
    echo "<h2>6. データベース接続</h2>";
    $db = new Database();
    echo "データベース接続: 成功<br>";
    
    $shop_id = $_SESSION['shop_id'];
    echo "店舗ID: " . $shop_id . "<br>";
    
    echo "<h2>7. 住所変更情報確認</h2>";
    try {
        $address_change_info = $db->fetch(
            "SELECT * FROM shop_address_changes 
             WHERE shop_id = ? AND status = 'pending' 
             ORDER BY created_at DESC LIMIT 1",
            [$shop_id]
        );
        
        if ($address_change_info) {
            echo "住所変更情報: 存在<br>";
            echo "<pre>";
            print_r($address_change_info);
            echo "</pre>";
        } else {
            echo "住所変更情報: 存在しない<br>";
        }
    } catch (Exception $e) {
        echo "住所変更情報取得エラー: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>8. POSTデータ確認</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    if ($_POST && isset($_POST['verify_code'])) {
        echo "<h2>9. 確認コード処理</h2>";
        $input_code = $_POST['verification_code'] ?? '';
        echo "入力コード: " . htmlspecialchars($input_code) . "<br>";
        
        if (empty($input_code)) {
            echo "確認コードが入力されていません<br>";
        } else {
            // 確認コードの検証処理
            echo "確認コード検証処理を実行します<br>";
        }
    } else {
        echo "<h2>9. 確認コード入力フォーム</h2>";
        ?>
        <form method="POST">
            <div>
                <label>確認コード（6桁）:</label>
                <input type="text" name="verification_code" maxlength="6" pattern="[0-9]{6}" required>
            </div>
            <div>
                <button type="submit" name="verify_code">確認</button>
            </div>
        </form>
        <?php
    }
    
} catch (Exception $e) {
    echo "<h2>エラー発生</h2>";
    echo "エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "エラーファイル: " . $e->getFile() . "<br>";
    echo "エラー行: " . $e->getLine() . "<br>";
    echo "<pre>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<h2>現在のセッションデータ</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
