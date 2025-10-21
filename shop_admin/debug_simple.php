<?php
// シンプルなデバッグファイル
echo "1. PHP is working<br>";

// セッション開始
session_start();
echo "2. Session started<br>";

// ファイル存在確認
$files_to_check = [
    '../includes/error_logger.php',
    '../config/config.php',
    '../includes/functions.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "3. File exists: $file<br>";
    } else {
        echo "3. File NOT found: $file<br>";
    }
}

// ディレクトリ確認
$dirs_to_check = [
    '../logs',
    '../config',
    '../includes'
];

foreach ($dirs_to_check as $dir) {
    if (is_dir($dir)) {
        echo "4. Directory exists: $dir<br>";
        if (is_writable($dir)) {
            echo "4. Directory is writable: $dir<br>";
        } else {
            echo "4. Directory is NOT writable: $dir<br>";
        }
    } else {
        echo "4. Directory NOT found: $dir<br>";
    }
}

// セッション情報表示
echo "5. Session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// エラーログファイルの作成テスト
$log_file = '../logs/test.log';
$log_dir = dirname($log_file);

if (!is_dir($log_dir)) {
    echo "6. Creating logs directory<br>";
    mkdir($log_dir, 0755, true);
}

if (file_put_contents($log_file, "Test log entry at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND | LOCK_EX)) {
    echo "6. Successfully wrote to test log file<br>";
} else {
    echo "6. Failed to write to test log file<br>";
}

echo "7. Debug completed<br>";
?>
