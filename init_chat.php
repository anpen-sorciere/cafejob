<?php
/**
 * チャット・BBS機能の初期化スクリプト
 * データベーステーブルを作成し、サンプルデータを投入
 */

require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "チャット・BBS機能の初期化を開始します...\n\n";
    
    // SQLファイルを読み込み
    $sql_file = __DIR__ . '/database/06_chat_tables.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("SQLファイルが見つかりません: $sql_file");
    }
    
    $sql = file_get_contents($sql_file);
    
    // SQLを分割して実行
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $connection->exec($statement);
            echo "✓ SQL実行完了: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\n=== チャット・BBS機能の初期化完了 ===\n";
    echo "作成されたテーブル:\n";
    echo "- chat_rooms (チャットルーム)\n";
    echo "- chat_messages (チャットメッセージ)\n";
    echo "- chat_notifications (チャット通知)\n";
    
    // テーブル確認
    $tables = ['chat_rooms', 'chat_messages', 'chat_notifications'];
    foreach ($tables as $table) {
        $stmt = $connection->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "- $table: $count 件\n";
    }
    
} catch (Exception $e) {
    echo "エラーが発生しました: " . $e->getMessage() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
}
?>
