-- カフェJob データベーススキーマ（口コミ・お気に入り関連テーブル）
-- ステップ4: 口コミ・お気に入り関連テーブルの作成

-- 口コミテーブル
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_id INT NOT NULL,
    user_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(100),
    content TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- お気に入りテーブル
CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    shop_id INT,
    job_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    CHECK ((shop_id IS NOT NULL AND job_id IS NULL) OR (shop_id IS NULL AND job_id IS NOT NULL))
);



