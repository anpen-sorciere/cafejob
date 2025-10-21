-- カフェJob データベーススキーマ（本番環境用）
-- MySQL 8.0以上対応
-- データベース名: purplelion51_cafejob

-- 注意: データベースは既に存在するため、CREATE DATABASE文は不要
-- USE purplelion51_cafejob;

-- 都道府県テーブル
CREATE TABLE prefectures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 市区町村テーブル
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prefecture_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prefecture_id) REFERENCES prefectures(id)
);

-- 店舗テーブル
CREATE TABLE shops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    address VARCHAR(200),
    prefecture_id INT,
    city_id INT,
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(200),
    opening_hours TEXT,
    concept_type ENUM('maid', 'butler', 'idol', 'cosplay', 'other') DEFAULT 'other',
    uniform_type VARCHAR(50),
    image_url VARCHAR(200),
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prefecture_id) REFERENCES prefectures(id),
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

-- ユーザーテーブル（求職者）
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    profile_image VARCHAR(200),
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- キャストテーブル
CREATE TABLE casts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    nickname VARCHAR(50),
    age INT,
    height INT,
    blood_type VARCHAR(5),
    hobby TEXT,
    special_skill TEXT,
    profile_image VARCHAR(200),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

-- 求人テーブル
CREATE TABLE jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    job_type ENUM('part_time', 'full_time', 'contract') DEFAULT 'part_time',
    salary_min INT,
    salary_max INT,
    work_hours TEXT,
    requirements TEXT,
    benefits TEXT,
    gender_requirement ENUM('male', 'female', 'any') DEFAULT 'any',
    age_min INT,
    age_max INT,
    status ENUM('active', 'inactive', 'closed') DEFAULT 'active',
    application_deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

-- 応募テーブル
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT,
    status ENUM('pending', 'accepted', 'rejected', 'cancelled') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 管理者テーブル
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 店舗管理者テーブル
CREATE TABLE shop_admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

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

-- 初期データ挿入
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



