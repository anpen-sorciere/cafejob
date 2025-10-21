<?php
// 管理者パネル用の設定ファイル
// 管理者パネルからアクセスする際のパス調整

// 管理者パネルからのアクセスかどうかを判定
$is_admin_panel = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;

if ($is_admin_panel) {
    // 管理者パネルからのアクセスの場合
    require_once '../config/config.php';
    require_once '../config/database.php';
    require_once '../includes/functions.php';
} else {
    // 通常のページからのアクセスの場合
    require_once 'config/config.php';
    require_once 'config/database.php';
    require_once 'includes/functions.php';
}
?>



