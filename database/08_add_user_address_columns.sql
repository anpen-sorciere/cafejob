-- usersテーブルに住所関連カラムを追加
-- 既存のusersテーブルに住所情報を追加

ALTER TABLE users 
ADD COLUMN prefecture_id INT NULL AFTER gender,
ADD COLUMN city_id INT NULL AFTER prefecture_id,
ADD COLUMN address VARCHAR(255) NULL AFTER city_id,
ADD COLUMN postal_code VARCHAR(10) NULL AFTER address;

-- 外部キー制約を追加（prefecturesとcitiesテーブルが存在する場合）
-- ALTER TABLE users ADD FOREIGN KEY (prefecture_id) REFERENCES prefectures(id);
-- ALTER TABLE users ADD FOREIGN KEY (city_id) REFERENCES cities(id);

-- インデックスを追加
CREATE INDEX idx_users_prefecture ON users(prefecture_id);
CREATE INDEX idx_users_city ON users(city_id);
