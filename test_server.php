<?php
// 基本的なPHP動作確認
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "PHP動作確認<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "現在時刻: " . date('Y-m-d H:i:s') . "<br>";

// データベース接続テスト
try {
    $pdo = new PDO(
        "mysql:host=mysql2103.db.sakura.ne.jp;dbname=purplelion51_cafejob;charset=utf8mb4",
        "purplelion51",
        "-6r_am73",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "データベース接続: 成功<br>";
    
    // テーブル存在確認
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "テーブル数: " . count($tables) . "<br>";
    echo "テーブル一覧: " . implode(', ', $tables) . "<br>";
    
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage() . "<br>";
}

// ファイル存在確認
$files_to_check = [
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'includes/layout.php',
    'index.php'
];

echo "<br>ファイル存在確認:<br>";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✓ $file<br>";
    } else {
        echo "✗ $file (存在しません)<br>";
    }
}

// ディレクトリ権限確認
echo "<br>ディレクトリ権限確認:<br>";
$dirs_to_check = ['uploads', 'logs'];
foreach ($dirs_to_check as $dir) {
    if (is_dir($dir)) {
        echo "✓ $dir (ディレクトリ存在)<br>";
    } else {
        echo "✗ $dir (ディレクトリが存在しません)<br>";
    }
}

echo "<br>サーバー情報:<br>";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? '不明') . "<br>";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '不明') . "<br>";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? '不明') . "<br>";
?>
