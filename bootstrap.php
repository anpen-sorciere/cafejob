<?php
/**
 * プロジェクトルートパス自動検出設定
 * このファイルを読み込むことで、どこからでもconfig/config.phpを読み込める
 */

// プロジェクトルートを自動検出
if (!defined('PROJECT_ROOT')) {
    // このファイルの場所からプロジェクトルートを計算
    $current_dir = __DIR__;
    
    // 現在のディレクトリがプロジェクトルートかチェック
    if (file_exists($current_dir . '/config/config.php')) {
        define('PROJECT_ROOT', $current_dir);
    }
    // 1階層上（shop_admin等から実行時）
    elseif (file_exists(dirname($current_dir) . '/config/config.php')) {
        define('PROJECT_ROOT', dirname($current_dir));
    }
    // 2階層上（pages等から実行時）
    elseif (file_exists(dirname($current_dir, 2) . '/config/config.php')) {
        define('PROJECT_ROOT', dirname($current_dir, 2));
    }
    // 3階層上（admin等から実行時）
    elseif (file_exists(dirname($current_dir, 3) . '/config/config.php')) {
        define('PROJECT_ROOT', dirname($current_dir, 3));
    }
    else {
        // フォールバック: 現在のディレクトリをプロジェクトルートとする
        define('PROJECT_ROOT', $current_dir);
    }
}

// 絶対パスでconfigファイルを読み込み
$config_path = PROJECT_ROOT . '/config/config.php';
$database_path = PROJECT_ROOT . '/config/database.php';
$functions_path = PROJECT_ROOT . '/includes/functions.php';

// ファイル存在確認
if (!file_exists($config_path)) {
    throw new Exception("Config file not found: " . $config_path);
}

if (!file_exists($database_path)) {
    throw new Exception("Database config file not found: " . $database_path);
}

if (!file_exists($functions_path)) {
    throw new Exception("Functions file not found: " . $functions_path);
}

// ファイルを読み込み
require_once $config_path;
require_once $database_path;
require_once $functions_path;

// デバッグ用（開発時のみ）
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_log("Project root detected: " . PROJECT_ROOT);
    error_log("Config loaded from: " . $config_path);
}
