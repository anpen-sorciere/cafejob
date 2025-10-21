-- 求人テーブルのカラム追加
ALTER TABLE jobs 
ADD COLUMN salary_type ENUM('hourly', 'monthly', 'daily') DEFAULT 'hourly' AFTER salary_max,
ADD COLUMN location VARCHAR(100) AFTER work_hours;
