-- カフェJob データベーススキーマ（求人・応募関連テーブル）
-- ステップ3: 求人・応募関連テーブルの作成

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

