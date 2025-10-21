<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>shop_admin/verify_address.php 構文チェック</h1>";

// ファイルの存在確認
$file_path = 'shop_admin/verify_address.php';
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
    
    // ファイルの内容を一部表示
    echo "<h3>ファイル内容（最初の50行）:</h3>";
    $lines = file($file_path);
    echo "<pre>";
    for ($i = 0; $i < min(50, count($lines)); $i++) {
        echo ($i + 1) . ": " . htmlspecialchars($lines[$i]);
    }
    echo "</pre>";
    
} else {
    echo "<p style='color: red;'>❌ ファイルが存在しません</p>";
}

echo "<h2>構文チェック完了</h2>";
?>
