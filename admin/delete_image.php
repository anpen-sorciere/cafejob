<?php
/**
 * 画像削除処理API
 * 管理者が不適切な画像を削除するためのAPI
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// システム管理者認証チェック
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => '認証が必要です']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'POSTメソッドが必要です']);
    exit;
}

$message_id = $_POST['message_id'] ?? null;

if (!$message_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'メッセージIDが必要です']);
    exit;
}

try {
    $db = new Database();
    
    // メッセージ情報を取得
    $message = $db->fetch("
        SELECT cm.*, cr.id as room_id
        FROM chat_messages cm
        JOIN chat_rooms cr ON cm.room_id = cr.id
        WHERE cm.id = ? AND cm.message_type = 'image'
    ", [$message_id]);
    
    if (!$message) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'メッセージが見つかりません']);
        exit;
    }
    
    // ファイルを削除
    if ($message['file_path'] && file_exists($message['file_path'])) {
        unlink($message['file_path']);
    }
    
    // データベースからメッセージを削除
    $db->query("DELETE FROM chat_messages WHERE id = ?", [$message_id]);
    
    // 通知も削除
    $db->query("DELETE FROM chat_notifications WHERE message_id = ?", [$message_id]);
    
    echo json_encode(['success' => true, 'message' => '画像を削除しました']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => '削除処理中にエラーが発生しました: ' . $e->getMessage()]);
}
?>

