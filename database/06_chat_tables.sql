-- チャット・BBS機能用テーブル
-- 応募キャストと店舗の個人的なやり取り

-- チャットルームテーブル
CREATE TABLE chat_rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    shop_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('active', 'closed', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application_room (application_id)
);

-- チャットメッセージテーブル
CREATE TABLE chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    sender_type ENUM('user', 'shop_admin') NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('text', 'image', 'file') DEFAULT 'text',
    file_path VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE
);

-- チャット通知テーブル
CREATE TABLE chat_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    recipient_type ENUM('user', 'shop_admin') NOT NULL,
    recipient_id INT NOT NULL,
    message_id INT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE
);

-- インデックス
CREATE INDEX idx_chat_rooms_shop_user ON chat_rooms(shop_id, user_id);
CREATE INDEX idx_chat_rooms_application ON chat_rooms(application_id);
CREATE INDEX idx_chat_messages_room ON chat_messages(room_id);
CREATE INDEX idx_chat_messages_created ON chat_messages(created_at);
CREATE INDEX idx_chat_notifications_recipient ON chat_notifications(recipient_type, recipient_id);
CREATE INDEX idx_chat_notifications_unread ON chat_notifications(is_read);
