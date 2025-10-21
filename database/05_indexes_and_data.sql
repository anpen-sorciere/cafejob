-- カフェJob データベーススキーマ（インデックス・初期データ）
-- ステップ5: インデックスと初期データの挿入

-- インデックス作成
CREATE INDEX idx_shops_prefecture ON shops(prefecture_id);
CREATE INDEX idx_shops_city ON shops(city_id);
CREATE INDEX idx_shops_status ON shops(status);
CREATE INDEX idx_jobs_shop ON jobs(shop_id);
CREATE INDEX idx_jobs_status ON jobs(status);
CREATE INDEX idx_applications_job ON applications(job_id);
CREATE INDEX idx_applications_user ON applications(user_id);
CREATE INDEX idx_casts_shop ON casts(shop_id);
CREATE INDEX idx_reviews_shop ON reviews(shop_id);

-- 都道府県データの挿入
INSERT INTO prefectures (name) VALUES 
('北海道'), ('青森県'), ('岩手県'), ('宮城県'), ('秋田県'), ('山形県'), ('福島県'),
('茨城県'), ('栃木県'), ('群馬県'), ('埼玉県'), ('千葉県'), ('東京都'), ('神奈川県'),
('新潟県'), ('富山県'), ('石川県'), ('福井県'), ('山梨県'), ('長野県'), ('岐阜県'),
('静岡県'), ('愛知県'), ('三重県'), ('滋賀県'), ('京都府'), ('大阪府'), ('兵庫県'),
('奈良県'), ('和歌山県'), ('鳥取県'), ('島根県'), ('岡山県'), ('広島県'), ('山口県'),
('徳島県'), ('香川県'), ('愛媛県'), ('高知県'), ('福岡県'), ('佐賀県'), ('長崎県'),
('熊本県'), ('大分県'), ('宮崎県'), ('鹿児島県'), ('沖縄県');

-- サンプル管理者アカウント（パスワード: admin123）
INSERT INTO admins (username, email, password_hash, role) VALUES 
('admin', 'admin@cafejob.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- サンプルユーザーアカウント（パスワード: demo123）
INSERT INTO users (username, email, password_hash, first_name, last_name, status) VALUES 
('demo_user', 'demo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'デモ', 'ユーザー', 'active');



