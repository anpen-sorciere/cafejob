-- 住所変更時の郵便確認機能用テーブル

-- 確認コード入力ミス履歴テーブル
CREATE TABLE verification_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_id INT NOT NULL,
    attempt_type ENUM('initial_registration', 'address_change') NOT NULL,
    verification_code VARCHAR(6),
    input_code VARCHAR(6),
    ip_address VARCHAR(45),
    user_agent TEXT,
    is_successful BOOLEAN DEFAULT FALSE,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

-- 店舗住所変更履歴テーブルに失敗回数カラムを追加
ALTER TABLE shop_address_changes 
ADD COLUMN failed_attempts INT DEFAULT 0 AFTER verification_code,
ADD COLUMN is_locked BOOLEAN DEFAULT FALSE AFTER failed_attempts,
ADD COLUMN locked_at TIMESTAMP NULL AFTER is_locked;

-- 店舗テーブルに住所確認状態を追加
ALTER TABLE shops 
ADD COLUMN address_verification_status ENUM('verified', 'pending', 'locked') DEFAULT 'verified' AFTER status,
ADD COLUMN address_verification_locked_at TIMESTAMP NULL AFTER address_verification_status;

-- インデックス
CREATE INDEX idx_shop_address_changes_shop ON shop_address_changes(shop_id);
CREATE INDEX idx_shop_address_changes_status ON shop_address_changes(status);
CREATE INDEX idx_shop_address_changes_verification_code ON shop_address_changes(verification_code);
