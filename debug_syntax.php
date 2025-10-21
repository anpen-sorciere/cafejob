<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP構文チェック</h1>";

// pages/shop_register.phpの構文チェック
echo "<h2>1. pages/shop_register.php の構文チェック</h2>";

$file_path = 'pages/shop_register.php';
if (file_exists($file_path)) {
    echo "<p>ファイル存在: ✅</p>";
    
    // 構文チェック
    $output = shell_exec("php -l " . escapeshellarg($file_path) . " 2>&1");
    echo "<h3>構文チェック結果:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<p style='color: green;'>✅ 構文エラーなし</p>";
    } else {
        echo "<p style='color: red;'>❌ 構文エラーあり</p>";
    }
} else {
    echo "<p style='color: red;'>❌ ファイルが存在しません</p>";
}

echo "<h2>2. ファイルの読み込みテスト</h2>";

try {
    echo "<p>config.php読み込みテスト...</p>";
    require_once 'config/config.php';
    echo "<p>✅ config.php loaded</p>";
    
    echo "<p>functions.php読み込みテスト...</p>";
    require_once 'includes/functions.php';
    echo "<p>✅ functions.php loaded</p>";
    
    echo "<p>データベース接続テスト...</p>";
    if (isset($db) && $db) {
        echo "<p>✅ Database connection available</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection not available</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>3. shop_register.php の段階的読み込みテスト</h2>";

try {
    echo "<p>セッション開始...</p>";
    session_start();
    echo "<p>✅ Session started</p>";
    
    echo "<p>変数初期化...</p>";
    $page_title = '店舗登録';
    $page_description = 'コンカフェの店舗登録を行います。';
    echo "<p>✅ Variables initialized</p>";
    
    echo "<p>POST処理の開始...</p>";
    if ($_POST && isset($_POST['register'])) {
        echo "<p>POST data detected</p>";
        
        // 各フィールドの値を取得
        $name = sanitize_input($_POST['name'] ?? '');
        echo "<p>店舗名: " . htmlspecialchars($name) . "</p>";
        
        $description = sanitize_input($_POST['description'] ?? '');
        echo "<p>説明: " . htmlspecialchars($description) . "</p>";
        
        $address = sanitize_input($_POST['address'] ?? '');
        echo "<p>住所: " . htmlspecialchars($address) . "</p>";
        
        $prefecture_id = (int)($_POST['prefecture_id'] ?? 0);
        echo "<p>都道府県ID: " . $prefecture_id . "</p>";
        
        $city_id = sanitize_input($_POST['city_id'] ?? '');
        echo "<p>市区町村: " . htmlspecialchars($city_id) . "</p>";
        
        $phone = sanitize_input($_POST['phone'] ?? '');
        echo "<p>電話番号: " . htmlspecialchars($phone) . "</p>";
        
        $email = sanitize_input($_POST['email'] ?? '');
        echo "<p>メールアドレス: " . htmlspecialchars($email) . "</p>";
        
        $website = sanitize_input($_POST['website'] ?? '');
        echo "<p>ウェブサイト: " . htmlspecialchars($website) . "</p>";
        
        $opening_hours = sanitize_input($_POST['opening_hours'] ?? '');
        echo "<p>営業時間: " . htmlspecialchars($opening_hours) . "</p>";
        
        $concept_type = sanitize_input($_POST['concept_type'] ?? '');
        echo "<p>コンセプト: " . htmlspecialchars($concept_type) . "</p>";
        
        $uniform_type = sanitize_input($_POST['uniform_type'] ?? '');
        echo "<p>制服: " . htmlspecialchars($uniform_type) . "</p>";
        
        // 店舗管理者情報
        $admin_last_name = sanitize_input($_POST['admin_last_name'] ?? '');
        echo "<p>管理者姓: " . htmlspecialchars($admin_last_name) . "</p>";
        
        $admin_first_name = sanitize_input($_POST['admin_first_name'] ?? '');
        echo "<p>管理者名: " . htmlspecialchars($admin_first_name) . "</p>";
        
        $admin_email = sanitize_input($_POST['admin_email'] ?? '');
        echo "<p>管理者メール: " . htmlspecialchars($admin_email) . "</p>";
        
        $admin_email_confirm = sanitize_input($_POST['admin_email_confirm'] ?? '');
        echo "<p>管理者メール確認: " . htmlspecialchars($admin_email_confirm) . "</p>";
        
        $admin_password = $_POST['admin_password'] ?? '';
        echo "<p>パスワード: " . (empty($admin_password) ? '空' : '入力済み') . "</p>";
        
        $admin_password_confirm = $_POST['admin_password_confirm'] ?? '';
        echo "<p>パスワード確認: " . (empty($admin_password_confirm) ? '空' : '入力済み') . "</p>";
        
        echo "<p>✅ POST data processing completed</p>";
        
    } else {
        echo "<p>POST data not received</p>";
    }
    
    echo "<p>都道府県データ取得...</p>";
    $prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id");
    echo "<p>✅ Prefectures loaded: " . count($prefectures) . " items</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during processing: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>4. エラーログ確認</h2>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    echo "<p>エラーログファイル: " . $error_log . "</p>";
    $log_content = file_get_contents($error_log);
    $recent_errors = array_slice(explode("\n", $log_content), -20);
    echo "<h3>最近のエラー:</h3>";
    echo "<pre>" . htmlspecialchars(implode("\n", $recent_errors)) . "</pre>";
} else {
    echo "<p>エラーログファイルが見つかりません</p>";
}

echo "<h2>構文チェック完了</h2>";
?>
