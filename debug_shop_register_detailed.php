<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>店舗登録ページ デバッグ</h1>";

try {
    echo "<h2>1. 基本ファイル読み込みテスト</h2>";
    
    require_once 'config/config.php';
    echo "<p>✅ config.php loaded</p>";
    
    require_once 'includes/functions.php';
    echo "<p>✅ functions.php loaded</p>";
    
    echo "<h2>2. セッション開始</h2>";
    session_start();
    echo "<p>✅ Session started</p>";
    
    echo "<h2>3. 変数初期化テスト</h2>";
    $page_title = '店舗登録';
    $page_description = 'コンカフェの店舗登録を行います。';
    echo "<p>✅ Variables initialized</p>";
    
    echo "<h2>4. POST処理テスト</h2>";
    if ($_POST && isset($_POST['register'])) {
        echo "<p>POST data received</p>";
        
        // 各フィールドの値を取得してテスト
        $name = sanitize_input($_POST['name'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $address = sanitize_input($_POST['address'] ?? '');
        $prefecture_id = (int)($_POST['prefecture_id'] ?? 0);
        $city_id = sanitize_input($_POST['city_id'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $website = sanitize_input($_POST['website'] ?? '');
        $opening_hours = sanitize_input($_POST['opening_hours'] ?? '');
        $concept_type = sanitize_input($_POST['concept_type'] ?? '');
        $uniform_type = sanitize_input($_POST['uniform_type'] ?? '');
        
        // 店舗管理者情報
        $admin_last_name = sanitize_input($_POST['admin_last_name'] ?? '');
        $admin_first_name = sanitize_input($_POST['admin_first_name'] ?? '');
        $admin_email = sanitize_input($_POST['admin_email'] ?? '');
        $admin_email_confirm = sanitize_input($_POST['admin_email_confirm'] ?? '');
        $admin_password = $_POST['admin_password'] ?? '';
        $admin_password_confirm = $_POST['admin_password_confirm'] ?? '';
        
        echo "<p>✅ POST data processed</p>";
        
        echo "<h3>入力データ確認</h3>";
        echo "<ul>";
        echo "<li>店舗名: " . htmlspecialchars($name) . "</li>";
        echo "<li>都道府県ID: " . $prefecture_id . "</li>";
        echo "<li>市区町村: " . htmlspecialchars($city_id) . "</li>";
        echo "<li>住所: " . htmlspecialchars($address) . "</li>";
        echo "<li>管理者姓: " . htmlspecialchars($admin_last_name) . "</li>";
        echo "<li>管理者名: " . htmlspecialchars($admin_first_name) . "</li>";
        echo "<li>管理者メール: " . htmlspecialchars($admin_email) . "</li>";
        echo "</ul>";
        
        echo "<h2>5. バリデーションテスト</h2>";
        $errors = [];
        
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
        
        echo "<p>バリデーション完了。エラー数: " . count($errors) . "</p>";
        
        if (!empty($errors)) {
            echo "<h3>バリデーションエラー</h3>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<h2>6. データベース処理テスト</h2>";
            
            // ユニークチェック
            if ($db->fetch("SELECT id FROM shops WHERE name = ?", [$name])) {
                $errors[] = 'この店舗名は既に登録されています';
            }
            
            // 管理者のユーザー名は姓+名で自動生成
            $admin_username = $admin_last_name . $admin_first_name;
            
            if ($db->fetch("SELECT id FROM shop_admins WHERE email = ?", [$admin_email])) {
                $errors[] = 'このメールアドレスは既に使用されています';
            }
            
            echo "<p>ユニークチェック完了</p>";
            
            if (empty($errors)) {
                echo "<h3>データベース挿入テスト</h3>";
                
                try {
                    $db->getConnection()->beginTransaction();
                    echo "<p>✅ Transaction started</p>";
                    
                    // 6桁の確認コードを生成
                    $verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    echo "<p>確認コード生成: " . $verification_code . "</p>";
                    
                    // 店舗データを挿入
                    $full_address = !empty($city_id) ? $city_id . ' ' . $address : $address;
                    echo "<p>住所組み立て: " . htmlspecialchars($full_address) . "</p>";
                    
                    $sql = "INSERT INTO shops (name, description, address, prefecture_id, city_id, phone, email, website, 
                                               opening_hours, concept_type, uniform_type, status, verification_code, verification_sent_at, created_at) 
                             VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 'verification_pending', ?, NOW(), NOW())";
                    
                    echo "<p>SQL文: " . htmlspecialchars($sql) . "</p>";
                    
                    $shop_id = $db->query($sql, [
                        $name, $description, $full_address, $prefecture_id, $phone, $email, $website, 
                        $opening_hours, $concept_type, $uniform_type, $verification_code
                    ])->lastInsertId();
                    
                    echo "<p>✅ Shop inserted. ID: " . $shop_id . "</p>";
                    
                    // 店舗管理者データを挿入
                    $password_hash = hash_password($admin_password);
                    echo "<p>パスワードハッシュ生成完了</p>";
                    
                    $db->query(
                        "INSERT INTO shop_admins (shop_id, username, email, password_hash, status, created_at) 
                         VALUES (?, ?, ?, ?, 'active', NOW())",
                        [$shop_id, $admin_username, $admin_email, $password_hash]
                    );
                    
                    echo "<p>✅ Shop admin inserted</p>";
                    
                    $db->getConnection()->commit();
                    echo "<p>✅ Transaction committed</p>";
                    
                    echo "<h3>登録成功！</h3>";
                    echo "<p>確認コード: " . $verification_code . "</p>";
                    
                } catch (Exception $e) {
                    $db->getConnection()->rollBack();
                    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
                    echo "<pre>" . $e->getTraceAsString() . "</pre>";
                }
            }
        }
    } else {
        echo "<p>POST data not received</p>";
    }
    
    echo "<h2>7. 都道府県データ取得テスト</h2>";
    $prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id");
    echo "<p>都道府県数: " . count($prefectures) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>デバッグ完了</h2>";
?>
