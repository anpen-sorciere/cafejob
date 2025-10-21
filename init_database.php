<?php
// データベース初期化スクリプト
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>データベース初期化</h1>";

try {
    // 設定ファイルを読み込み
    require_once 'config/config.php';
    
    echo "設定読み込み完了<br>";
    echo "DB_HOST: " . DB_HOST . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
    echo "DB_USER: " . DB_USER . "<br>";
    
    // データベース接続
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "データベース接続成功<br>";
    
    // スキーマファイルを読み込み
    if (file_exists('database/schema.sql')) {
        $schema = file_get_contents('database/schema.sql');
        echo "スキーマファイル読み込み成功<br>";
        
        // SQL文を分割して実行
        $sql_statements = array_filter(array_map('trim', explode(';', $schema)));
        
        echo "SQL文数: " . count($sql_statements) . "<br>";
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($sql_statements as $sql) {
            if (!empty($sql) && !preg_match('/^--/', $sql)) {
                try {
                    $pdo->exec($sql);
                    $success_count++;
                } catch (PDOException $e) {
                    $error_count++;
                    echo "エラー: " . $e->getMessage() . "<br>";
                    echo "SQL: " . htmlspecialchars(substr($sql, 0, 100)) . "...<br>";
                }
            }
        }
        
        echo "<h2>実行結果</h2>";
        echo "成功: $success_count 件<br>";
        echo "エラー: $error_count 件<br>";
        
        // テーブル確認
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "作成されたテーブル数: " . count($tables) . "<br>";
        if (count($tables) > 0) {
            echo "テーブル一覧: " . implode(', ', $tables) . "<br>";
        }
        
    } else {
        echo "スキーマファイルが見つかりません: database/schema.sql<br>";
    }
    
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "<br>";
}

echo "<h2>初期化完了</h2>";
echo "<a href='index.php'>メインページに戻る</a><br>";
echo "<a href='debug_detailed.php'>診断ページに戻る</a>";
?>
