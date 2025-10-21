<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>店舗登録フォーム送信テスト</h1>";

try {
    require_once 'config/config.php';
    require_once 'includes/functions.php';
    
    echo "<h2>1. 基本設定確認</h2>";
    echo "<p>✅ Files loaded</p>";
    
    session_start();
    echo "<p>✅ Session started</p>";
    
    echo "<h2>2. テスト用POSTデータの作成</h2>";
    
    // テスト用のPOSTデータをシミュレート
    $_POST = [
        'register' => '1',
        'name' => 'テスト店舗',
        'description' => 'テスト説明',
        'address' => 'テスト住所',
        'prefecture_id' => '13', // 東京都
        'city_id' => '千代田区',
        'phone' => '03-1234-5678',
        'email' => 'test@example.com',
        'website' => 'https://example.com',
        'opening_hours' => '10:00-22:00',
        'concept_type' => 'maid',
        'uniform_type' => 'メイド服',
        'admin_last_name' => '田中',
        'admin_first_name' => '太郎',
        'admin_email' => 'admin@example.com',
        'admin_email_confirm' => 'admin@example.com',
        'admin_password' => 'password123',
        'admin_password_confirm' => 'password123'
    ];
    
    echo "<p>✅ Test POST data created</p>";
    
    echo "<h2>3. shop_register.php の処理を段階的に実行</h2>";
    
    // shop_register.phpの処理を段階的に実行
    $page_title = '店舗登録';
    $page_description = 'コンカフェの店舗登録を行います。';
    
    echo "<p>✅ Variables initialized</p>";
    
    // 店舗登録処理
    if ($_POST && isset($_POST['register'])) {
        echo "<p>POST処理開始...</p>";
        
        $name = sanitize_input($_POST['name']);
        $description = sanitize_input($_POST['description']);
        $address = sanitize_input($_POST['address']);
        $prefecture_id = (int)$_POST['prefecture_id'];
        $city_id = sanitize_input($_POST['city_id']);
        $phone = sanitize_input($_POST['phone']);
        $email = sanitize_input($_POST['email']);
        $website = sanitize_input($_POST['website']);
        $opening_hours = sanitize_input($_POST['opening_hours']);
        $concept_type = sanitize_input($_POST['concept_type']);
        $uniform_type = sanitize_input($_POST['uniform_type']);
        
        // 店舗管理者情報
        $admin_last_name = sanitize_input($_POST['admin_last_name']);
        $admin_first_name = sanitize_input($_POST['admin_first_name']);
        $admin_email = sanitize_input($_POST['admin_email']);
        $admin_email_confirm = sanitize_input($_POST['admin_email_confirm']);
        $admin_password = $_POST['admin_password'];
        $admin_password_confirm = $_POST['admin_password_confirm'];
        
        echo "<p>✅ Data sanitized</p>";
        
        $errors = [];
        
        // バリデーション
        if (empty($name)) $errors[] = '店舗名を入力してください';
        if (empty($description)) $errors[] = '店舗説明を入力してください';
        if (empty($address)) $errors[] = '住所を入力してください';
        if (!$prefecture_id) $errors[] = '都道府県を選択してください';
        if (empty($phone)) $errors[] = '電話番号を入力してください';
        if (empty($email) || !validate_email($email)) $errors[] = '有効なメールアドレスを入力してください';
        if (empty($admin_last_name)) $errors[] = '姓を入力してください';
        if (empty($admin_first_name)) $errors[] = '名を入力してください';
        if (empty($admin_email) || !validate_email($admin_email)) $errors[] = '有効なメールアドレスを入力してください';
        if (empty($admin_email_confirm) || !validate_email($admin_email_confirm)) $errors[] = '有効なメールアドレス確認を入力してください';
        if ($admin_email !== $admin_email_confirm) $errors[] = 'メールアドレスが一致しません';
        if (empty($admin_password)) $errors[] = 'パスワードを入力してください';
        if ($admin_password !== $admin_password_confirm) $errors[] = 'パスワードが一致しません';
        
        echo "<p>✅ Validation completed. Errors: " . count($errors) . "</p>";
        
        if (empty($errors)) {
            echo "<p>データベース処理開始...</p>";
            
            // ユニークチェック
            if ($db->fetch("SELECT id FROM shops WHERE name = ?", [$name])) {
                $errors[] = 'この店舗名は既に登録されています';
            }
            
            // 管理者のユーザー名は姓+名で自動生成
            $admin_username = $admin_last_name . $admin_first_name;
            
            if ($db->fetch("SELECT id FROM shop_admins WHERE email = ?", [$admin_email])) {
                $errors[] = 'このメールアドレスは既に使用されています';
            }
            
            echo "<p>✅ Unique check completed</p>";
            
            if (empty($errors)) {
                echo "<p>トランザクション開始...</p>";
                
                try {
                    $db->getConnection()->beginTransaction();
                    echo "<p>✅ Transaction started</p>";
                    
                    // 6桁の確認コードを生成
                    $verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    echo "<p>確認コード生成: " . $verification_code . "</p>";
                    
                    // 店舗データを挿入
                    $full_address = !empty($city_id) ? $city_id . ' ' . $address : $address;
                    echo "<p>住所組み立て: " . htmlspecialchars($full_address) . "</p>";
                    
                    $stmt = $db->query(
                        "INSERT INTO shops (name, description, address, prefecture_id, city_id, phone, email, website, 
                                           opening_hours, concept_type, uniform_type, status, verification_code, verification_sent_at, created_at) 
                         VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 'verification_pending', ?, NOW(), NOW())",
                        [$name, $description, $full_address, $prefecture_id, $phone, $email, $website, 
                         $opening_hours, $concept_type, $uniform_type, $verification_code]
                    );
                    $shop_id = $db->getConnection()->lastInsertId();
                    
                    echo "<p>✅ Shop inserted. ID: " . $shop_id . "</p>";
                    
                    // 店舗管理者データを挿入
                    $password_hash = hash_password($admin_password);
                    echo "<p>✅ Password hashed</p>";
                    
                    $db->query(
                        "INSERT INTO shop_admins (shop_id, username, email, password_hash, status, created_at) 
                         VALUES (?, ?, ?, ?, 'active', NOW())",
                        [$shop_id, $admin_username, $admin_email, $password_hash]
                    );
                    
                    echo "<p>✅ Shop admin inserted</p>";
                    
                    $db->getConnection()->commit();
                    echo "<p>✅ Transaction committed</p>";
                    
                    echo "<h2>🎉 登録成功！</h2>";
                    echo "<p>確認コード: " . $verification_code . "</p>";
                    echo "<p>店舗ID: " . $shop_id . "</p>";
                    
                } catch (Exception $e) {
                    $db->getConnection()->rollBack();
                    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
                    echo "<pre>" . $e->getTraceAsString() . "</pre>";
                }
            } else {
                echo "<h3>バリデーションエラー</h3>";
                foreach ($errors as $error) {
                    echo "<p style='color: red;'>❌ " . htmlspecialchars($error) . "</p>";
                }
            }
        } else {
            echo "<h3>バリデーションエラー</h3>";
            foreach ($errors as $error) {
                echo "<p style='color: red;'>❌ " . htmlspecialchars($error) . "</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>テスト完了</h2>";
?>
