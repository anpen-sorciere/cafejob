-- カフェJob データベーススキーマ（店舗関連テーブル）
-- ステップ2: 店舗関連テーブルの作成

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



