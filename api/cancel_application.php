<?php
// 応募キャンセルAPI
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// ログイン必須
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '無効なリクエストです']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$application_id = isset($input['application_id']) ? (int)$input['application_id'] : 0;

if (!$application_id) {
    echo json_encode(['success' => false, 'message' => '無効な応募IDです']);
    exit;
}

try {
    // 応募が存在し、ユーザーのものかチェック
    $application = $db->fetch(
        "SELECT * FROM applications WHERE id = ? AND user_id = ?",
        [$application_id, $_SESSION['user_id']]
    );
    
    if (!$application) {
        echo json_encode(['success' => false, 'message' => '応募が見つかりません']);
        exit;
    }
    
    if ($application['status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'キャンセルできない応募です']);
        exit;
    }
    
    // 応募をキャンセル
    $db->query(
        "UPDATE applications SET status = 'cancelled' WHERE id = ?",
        [$application_id]
    );
    
    echo json_encode(['success' => true, 'message' => '応募をキャンセルしました']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'エラーが発生しました: ' . $e->getMessage()]);
}
?>
