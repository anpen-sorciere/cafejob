<?php
$page_title = '店舗登録デバッグ';
$page_description = '店舗登録のデバッグを行います。';

// 店舗登録処理のデバッグ
if ($_POST && isset($_POST['register'])) {
    echo "<h1>店舗登録デバッグ</h1>";
    
    require_once 'config/config.php';
    require_once 'includes/functions.php';
    
    echo "<h2>1. POSTデータ確認</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>2. データ処理</h2>";
    
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $postal_code = sanitize_input($_POST['postal_code']);
    $address = sanitize_input($_POST['address']);
    $prefecture_id = (int)$_POST['prefecture_id'];
    $city_name = sanitize_input($_POST['city_id']); // 市区町村名をテキストとして取得
    $phone = sanitize_input($_POST['phone']);
    $email = sanitize_input($_POST['email']);
    $website = sanitize_input($_POST['website']);
    $opening_hours = sanitize_input($_POST['opening_hours']);
    $concept_type = sanitize_input($_POST['concept_type']);
    $uniform_type = sanitize_input($_POST['uniform_type']);
    
    // 管理者情報
    $admin_last_name = sanitize_input($_POST['admin_last_name']);
    $admin_first_name = sanitize_input($_POST['admin_first_name']);
    $admin_email = sanitize_input($_POST['admin_email']);
    $admin_email_confirm = sanitize_input($_POST['admin_email_confirm']);
    $admin_password = $_POST['admin_password'];
    $admin_password_confirm = $_POST['admin_password_confirm'];
    
    echo "<p>処理後のデータ:</p>";
    echo "<ul>";
    echo "<li>店舗名: " . htmlspecialchars($name) . "</li>";
    echo "<li>説明: " . htmlspecialchars($description) . "</li>";
    echo "<li>郵便番号: " . htmlspecialchars($postal_code) . "</li>";
    echo "<li>市区町村: " . htmlspecialchars($city_name) . "</li>";
    echo "<li>住所: " . htmlspecialchars($address) . "</li>";
    echo "<li>都道府県ID: " . $prefecture_id . "</li>";
    echo "<li>電話: " . htmlspecialchars($phone) . "</li>";
    echo "<li>メール: " . htmlspecialchars($email) . "</li>";
    echo "<li>管理者姓: " . htmlspecialchars($admin_last_name) . "</li>";
    echo "<li>管理者名: " . htmlspecialchars($admin_first_name) . "</li>";
    echo "<li>管理者メール: " . htmlspecialchars($admin_email) . "</li>";
    echo "<li>管理者メール確認: " . htmlspecialchars($admin_email_confirm) . "</li>";
    echo "<li>パスワード長: " . strlen($admin_password) . "</li>";
    echo "<li>パスワード確認長: " . strlen($admin_password_confirm) . "</li>";
    echo "</ul>";
    
    echo "<h2>3. バリデーション</h2>";
    
    $errors = [];
    
    // バリデーション
    if (empty($name)) $errors[] = '店舗名を入力してください';
    if (empty($description)) $errors[] = '店舗説明を入力してください';
    if (empty($postal_code)) $errors[] = '郵便番号を入力してください';
    if (empty($city_name)) $errors[] = '市区町村名を入力してください';
    if (empty($address)) $errors[] = '住所を入力してください';
    if (!$prefecture_id) $errors[] = '都道府県を選択してください';
    if (empty($phone)) $errors[] = '電話番号を入力してください';
    if (empty($email) || !validate_email($email)) $errors[] = '有効なメールアドレスを入力してください';
    if (empty($admin_last_name)) $errors[] = '姓を入力してください';
    if (empty($admin_first_name)) $errors[] = '名前を入力してください';
    if (empty($admin_email) || !validate_email($admin_email)) $errors[] = '有効なメールアドレスを入力してください';
    if (empty($admin_email_confirm) || !validate_email($admin_email_confirm)) $errors[] = '有効なメールアドレス確認を入力してください';
    if ($admin_email !== $admin_email_confirm) $errors[] = 'メールアドレスが一致しません';
    if (empty($admin_password)) $errors[] = 'パスワードを入力してください';
    if ($admin_password !== $admin_password_confirm) $errors[] = 'パスワードが一致しません';
    
    echo "<p>バリデーションエラー数: " . count($errors) . "</p>";
    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li style='color: red;'>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>バリデーション通過</p>";
    }
    
    echo "<h2>4. ユニークチェック</h2>";
    
    // ユニークチェック
    if ($db->fetch("SELECT id FROM shops WHERE name = ?", [$name])) {
        $errors[] = 'この店舗名は既に登録されています';
        echo "<p style='color: red;'>店舗名重複エラー</p>";
    } else {
        echo "<p style='color: green;'>店舗名重複チェック通過</p>";
    }
    
    if ($db->fetch("SELECT id FROM shop_admins WHERE email = ?", [$admin_email])) {
        $errors[] = 'このメールアドレスは既に登録されています';
        echo "<p style='color: red;'>管理者メール重複エラー</p>";
    } else {
        echo "<p style='color: green;'>管理者メール重複チェック通過</p>";
    }
    
    echo "<h2>5. データベース処理テスト</h2>";
    
    if (empty($errors)) {
        echo "<p>データベース処理を開始...</p>";
        
        try {
            $db->getConnection()->beginTransaction();
            
            // 6桁の確認コードを生成
            $verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            echo "<p>確認コード生成: " . $verification_code . "</p>";
            
            // 郵便番号を7桁の文字列として保存（先頭の0を保持）
            $postal_code_clean = str_replace('-', '', $postal_code);
            $postal_code_padded = str_pad($postal_code_clean, 7, '0', STR_PAD_LEFT);
            echo "<p>郵便番号処理: " . $postal_code . " → " . $postal_code_padded . "</p>";
            
            // 完全な住所を構築（市区町村名 + 詳細住所）
            $full_address = $city_name . $address;
            echo "<p>完全住所: " . htmlspecialchars($full_address) . "</p>";
            
            $stmt = $db->query(
                "INSERT INTO shops (name, description, postal_code, address, prefecture_id, city_id, phone, email, website, 
                                   opening_hours, concept_type, uniform_type, status, verification_code, verification_sent_at, created_at) 
                 VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 'verification_pending', ?, NOW(), NOW())",
                [$name, $description, $postal_code_padded, $full_address, $prefecture_id, $phone, $email, $website, 
                 $opening_hours, $concept_type, $uniform_type, $verification_code]
            );
            $shop_id = $db->getConnection()->lastInsertId();
            echo "<p style='color: green;'>店舗データ挿入成功。ID: " . $shop_id . "</p>";
            
            // 店舗管理者データを挿入
            $password_hash = hash_password($admin_password);
            $admin_username = $admin_last_name . $admin_first_name; // ユーザー名を姓+名で自動生成
            $db->query(
                "INSERT INTO shop_admins (shop_id, username, email, password_hash, status, created_at) 
                 VALUES (?, ?, ?, ?, 'active', NOW())",
                [$shop_id, $admin_username, $admin_email, $password_hash]
            );
            echo "<p style='color: green;'>管理者データ挿入成功</p>";
            
            $db->getConnection()->commit();
            echo "<p style='color: green; font-size: 18px; font-weight: bold;'>登録完了！</p>";
            
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            echo "<p style='color: red;'>データベースエラー: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>バリデーションエラーがあるため登録をスキップ</p>";
    }
    
} else {
    echo "<h1>店舗登録デバッグ</h1>";
    echo "<p>POSTデータがありません。店舗登録フォームから送信してください。</p>";
    echo "<p><a href='?page=shop_register'>店舗登録ページに戻る</a></p>";
}
?>
