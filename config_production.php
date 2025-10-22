<?php
// カフェJob 設定ファイル（本番環境用）
define('DB_HOST', 'mysql2103.db.sakura.ne.jp');
define('DB_NAME', 'purplelion51_cafejob');
define('DB_USER', 'purplelion51');
define('DB_PASS', '-6r_am73');
define('SITE_URL', 'https://purplelion51.sakura.ne.jp/cafejob');
define('SITE_NAME', 'カフェJob');
define('DEBUG_MODE', true);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// エラー表示設定
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}
?>
