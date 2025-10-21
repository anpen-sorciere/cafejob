<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>店舗登録デバッグ</h1>";

try {
    require_once 'config/config.php';
    echo "<p>✅ config.php loaded</p>";
    
    require_once 'includes/functions.php';
    echo "<p>✅ functions.php loaded</p>";
    
    // データベース接続テスト
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Database connection successful</p>";
    
    // shopsテーブルの構造確認
    $stmt = $pdo->query("DESCRIBE shops");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>shopsテーブルの構造</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 必要なカラムの存在確認
    $required_columns = ['verification_code', 'verification_sent_at', 'verification_verified_at'];
    $existing_columns = array_column($columns, 'Field');
    
    echo "<h2>必要なカラムの存在確認</h2>";
    foreach ($required_columns as $col) {
        if (in_array($col, $existing_columns)) {
            echo "<p style='color: green;'>✅ $col - 存在</p>";
        } else {
            echo "<p style='color: red;'>❌ $col - 存在しない</p>";
        }
    }
    
    // テスト用のINSERT文を実行してみる
    echo "<h2>テスト用INSERT文</h2>";
    try {
        $test_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        echo "<p>生成された確認コード: $test_code</p>";
        
        $sql = "INSERT INTO shops (name, description, address, prefecture_id, city_id, phone, email, website, 
                                   opening_hours, concept_type, uniform_type, status, verification_code, verification_sent_at, created_at) 
                 VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 'verification_pending', ?, NOW(), NOW())";
        
        echo "<p>SQL文: " . htmlspecialchars($sql) . "</p>";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'テスト店舗',
            'テスト説明',
            'テスト住所',
            13, // 東京都
            '03-1234-5678',
            'test@example.com',
            'https://example.com',
            '10:00-22:00',
            'maid',
            'メイド服',
            $test_code
        ]);
        
        if ($result) {
            $shop_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>✅ テストINSERT成功 - Shop ID: $shop_id</p>";
            
            // テストデータを削除
            $pdo->exec("DELETE FROM shops WHERE id = $shop_id");
            echo "<p>テストデータを削除しました</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ テストINSERT失敗: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ エラー: " . $e->getMessage() . "</p>";
    echo "<p>スタックトレース:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
