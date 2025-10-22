-- 住所変更時の郵便確認機能用テーブル

-- 店舗住所変更履歴テーブル
CREATE TABLE shop_address_changes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_id INT NOT NULL,
    old_postal_code VARCHAR(7),
    old_prefecture_id INT,
    old_city_name VARCHAR(100),
    old_address VARCHAR(255),
    new_postal_code VARCHAR(7),
    new_prefecture_id INT,
    new_city_name VARCHAR(100),
    new_address VARCHAR(255),
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verification_code VARCHAR(6),
    verification_sent_at TIMESTAMP NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (old_prefecture_id) REFERENCES prefectures(id) ON DELETE SET NULL,
    FOREIGN KEY (new_prefecture_id) REFERENCES prefectures(id) ON DELETE SET NULL
);

-- 店舗テーブルに住所確認状態を追加
ALTER TABLE shops 
ADD COLUMN address_verification_status ENUM('verified', 'pending', 'locked') DEFAULT 'verified' AFTER status,
ADD COLUMN address_verification_locked_at TIMESTAMP NULL AFTER address_verification_status;

-- インデックス
CREATE INDEX idx_shop_address_changes_shop ON shop_address_changes(shop_id);
CREATE INDEX idx_shop_address_changes_status ON shop_address_changes(status);
CREATE INDEX idx_shop_address_changes_verification_code ON shop_address_changes(verification_code);
