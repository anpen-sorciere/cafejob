<?php
// サンプルデータ投入機能
session_start();
require_once 'includes.php';

// 管理者認証チェック
if (!is_admin()) {
    header('Location: ../?page=admin_login');
    exit;
}

$page_title = 'サンプルデータ投入';

// データ投入処理
if ($_POST && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'insert_sample_data':
                insertSampleData();
                $_SESSION['success_message'] = 'サンプルデータの投入が完了しました。';
                break;
            case 'clear_all_data':
                clearAllData();
                $_SESSION['success_message'] = 'すべてのデータを削除しました。';
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'エラーが発生しました: ' . $e->getMessage();
    }
    header('Location: sample_data.php');
    exit;
}

function insertSampleData() {
    global $db;
    
    // まず都道府県と市区町村データを挿入
    insertPrefectureAndCityData();
    
    // サンプル店舗データ
    $sample_shops = [
        [
            'name' => 'メイドカフェ・ミアカフェ秋葉原店',
            'description' => '2004年創業の老舗メイドカフェ。秋葉原の中心地で、お客様をおもてなししています。',
            'address' => '東京都千代田区外神田1-2-3',
            'prefecture_id' => 13,
            'city_id' => 1,
            'phone' => '03-1234-5678',
            'email' => 'info@miacafe.com',
            'website' => 'https://miacafe.com',
            'opening_hours' => '平日 11:00-22:00\n土日祝 10:00-23:00',
            'concept_type' => 'maid',
            'uniform_type' => 'メイド服',
            'image_url' => 'https://via.placeholder.com/400x300/FF69B4/FFFFFF?text=Maid+Cafe',
            'status' => 'active'
        ],
        [
            'name' => 'コンカフェ And Lovely',
            'description' => '秋葉原で人気No.1のコンカフェ。可愛いキャストがお客様をお迎えします。',
            'address' => '東京都千代田区外神田2-3-4',
            'prefecture_id' => 13,
            'city_id' => 1,
            'phone' => '03-2345-6789',
            'email' => 'info@andlovely.com',
            'website' => 'https://andlovely.com',
            'opening_hours' => '平日 12:00-23:00\n土日祝 11:00-24:00',
            'concept_type' => 'maid',
            'uniform_type' => 'ロリータ服',
            'image_url' => 'https://via.placeholder.com/400x300/FF1493/FFFFFF?text=And+Lovely',
            'status' => 'active'
        ],
        [
            'name' => '執事喫茶 黒執事',
            'description' => '上品な執事がおもてなしする執事喫茶。落ち着いた雰囲気でお楽しみいただけます。',
            'address' => '東京都渋谷区道玄坂1-2-3',
            'prefecture_id' => 13,
            'city_id' => 2,
            'phone' => '03-3456-7890',
            'email' => 'info@kuroshitsuji.com',
            'website' => 'https://kuroshitsuji.com',
            'opening_hours' => '平日 14:00-22:00\n土日祝 12:00-23:00',
            'concept_type' => 'butler',
            'uniform_type' => '執事服',
            'image_url' => 'https://via.placeholder.com/400x300/000000/FFFFFF?text=Butler+Cafe',
            'status' => 'active'
        ],
        [
            'name' => 'アイドルカフェ スターダスト',
            'description' => 'アイドルを目指すキャストが歌とダンスでおもてなし。ライブも開催しています。',
            'address' => '東京都新宿区歌舞伎町1-2-3',
            'prefecture_id' => 13,
            'city_id' => 3,
            'phone' => '03-4567-8901',
            'email' => 'info@stardust.com',
            'website' => 'https://stardust.com',
            'opening_hours' => '平日 18:00-24:00\n土日祝 16:00-25:00',
            'concept_type' => 'idol',
            'uniform_type' => 'アイドル衣装',
            'image_url' => 'https://via.placeholder.com/400x300/FFD700/000000?text=Idol+Cafe',
            'status' => 'active'
        ],
        [
            'name' => 'コスプレカフェ コスモス',
            'description' => '様々なコスプレでおもてなし。アニメ・ゲームの世界観を楽しめます。',
            'address' => '大阪府大阪市北区梅田1-2-3',
            'prefecture_id' => 27,
            'city_id' => 4,
            'phone' => '06-1234-5678',
            'email' => 'info@cosmos.com',
            'website' => 'https://cosmos.com',
            'opening_hours' => '平日 17:00-23:00\n土日祝 15:00-24:00',
            'concept_type' => 'cosplay',
            'uniform_type' => 'コスプレ衣装',
            'image_url' => 'https://via.placeholder.com/400x300/9370DB/FFFFFF?text=Cosplay+Cafe',
            'status' => 'active'
        ]
    ];
    
    // 店舗データを挿入
    foreach ($sample_shops as $shop_data) {
        $db->query(
            "INSERT INTO shops (name, description, address, prefecture_id, city_id, phone, email, website, opening_hours, concept_type, uniform_type, image_url, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($shop_data)
        );
    }
    
    // サンプル求人データ
    $shop_ids = $db->fetchAll("SELECT id FROM shops ORDER BY id DESC LIMIT 5");
    
    $sample_jobs = [
        [
            'shop_id' => $shop_ids[0]['id'],
            'title' => 'メイドカフェスタッフ募集',
            'description' => 'お客様をおもてなしするメイドスタッフを募集しています。未経験者も大歓迎！',
            'job_type' => 'part_time',
            'salary_min' => 1000,
            'salary_max' => 1200,
            'work_hours' => '平日 11:00-19:00\n土日祝 10:00-20:00',
            'requirements' => '18歳以上、明るい性格、接客が好きな方',
            'benefits' => '交通費支給、制服貸与、研修制度完備',
            'gender_requirement' => 'female',
            'age_min' => 18,
            'age_max' => 30,
            'status' => 'active',
            'application_deadline' => date('Y-m-d', strtotime('+30 days'))
        ],
        [
            'shop_id' => $shop_ids[1]['id'],
            'title' => 'コンカフェホールスタッフ',
            'description' => '可愛い制服でお客様をお迎えするホールスタッフを募集！',
            'job_type' => 'part_time',
            'salary_min' => 950,
            'salary_max' => 1100,
            'work_hours' => '平日 12:00-20:00\n土日祝 11:00-21:00',
            'requirements' => '18歳以上、笑顔が素敵な方',
            'benefits' => '交通費支給、制服貸与、ボーナス制度',
            'gender_requirement' => 'female',
            'age_min' => 18,
            'age_max' => 25,
            'status' => 'active',
            'application_deadline' => date('Y-m-d', strtotime('+25 days'))
        ],
        [
            'shop_id' => $shop_ids[2]['id'],
            'title' => '執事喫茶スタッフ',
            'description' => '上品な執事としてお客様をおもてなしするスタッフを募集。',
            'job_type' => 'part_time',
            'salary_min' => 1100,
            'salary_max' => 1300,
            'work_hours' => '平日 14:00-22:00\n土日祝 12:00-23:00',
            'requirements' => '20歳以上、礼儀正しい方、接客経験者優遇',
            'benefits' => '交通費支給、制服貸与、研修制度',
            'gender_requirement' => 'male',
            'age_min' => 20,
            'age_max' => 35,
            'status' => 'active',
            'application_deadline' => date('Y-m-d', strtotime('+20 days'))
        ],
        [
            'shop_id' => $shop_ids[3]['id'],
            'title' => 'アイドルカフェパフォーマー',
            'description' => '歌とダンスでお客様を楽しませるパフォーマーを募集！',
            'job_type' => 'part_time',
            'salary_min' => 1200,
            'salary_max' => 1500,
            'work_hours' => '平日 18:00-24:00\n土日祝 16:00-25:00',
            'requirements' => '18歳以上、歌やダンスが好きな方',
            'benefits' => '交通費支給、衣装貸与、ライブ出演機会',
            'gender_requirement' => 'female',
            'age_min' => 18,
            'age_max' => 28,
            'status' => 'active',
            'application_deadline' => date('Y-m-d', strtotime('+15 days'))
        ],
        [
            'shop_id' => $shop_ids[4]['id'],
            'title' => 'コスプレカフェスタッフ',
            'description' => '様々なコスプレでお客様をおもてなしするスタッフを募集！',
            'job_type' => 'part_time',
            'salary_min' => 900,
            'salary_max' => 1100,
            'work_hours' => '平日 17:00-23:00\n土日祝 15:00-24:00',
            'requirements' => '18歳以上、アニメ・ゲームが好きな方',
            'benefits' => '交通費支給、コスプレ衣装貸与',
            'gender_requirement' => 'any',
            'age_min' => 18,
            'age_max' => 30,
            'status' => 'active',
            'application_deadline' => date('Y-m-d', strtotime('+35 days'))
        ]
    ];
    
    // 求人データを挿入
    foreach ($sample_jobs as $job_data) {
        $db->query(
            "INSERT INTO jobs (shop_id, title, description, job_type, salary_min, salary_max, work_hours, requirements, benefits, gender_requirement, age_min, age_max, status, application_deadline) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($job_data)
        );
    }
    
    // サンプルキャストデータ
    $cast_names = [
        ['name' => 'みお', 'nickname' => 'みおちゃん', 'age' => 22, 'height' => 158, 'blood_type' => 'A', 'hobby' => '読書、映画鑑賞', 'special_skill' => 'お茶の淹れ方'],
        ['name' => 'あいり', 'nickname' => 'あいりん', 'age' => 20, 'height' => 155, 'blood_type' => 'O', 'hobby' => 'ダンス、歌', 'special_skill' => 'ダンス'],
        ['name' => 'ゆき', 'nickname' => 'ゆきちゃん', 'age' => 24, 'height' => 162, 'blood_type' => 'B', 'hobby' => '料理、お菓子作り', 'special_skill' => 'ケーキ作り'],
        ['name' => 'まい', 'nickname' => 'まいちゃん', 'age' => 19, 'height' => 156, 'blood_type' => 'AB', 'hobby' => 'アニメ、ゲーム', 'special_skill' => 'ゲーム'],
        ['name' => 'りん', 'nickname' => 'りんちゃん', 'age' => 21, 'height' => 160, 'blood_type' => 'A', 'hobby' => '音楽、楽器演奏', 'special_skill' => 'ピアノ']
    ];
    
    // キャストデータを挿入
    foreach ($shop_ids as $index => $shop) {
        if (isset($cast_names[$index])) {
            $cast = $cast_names[$index];
            $db->query(
                "INSERT INTO casts (shop_id, name, nickname, age, height, blood_type, hobby, special_skill, profile_image, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $shop['id'],
                    $cast['name'],
                    $cast['nickname'],
                    $cast['age'],
                    $cast['height'],
                    $cast['blood_type'],
                    $cast['hobby'],
                    $cast['special_skill'],
                    'https://via.placeholder.com/200x200/FF69B4/FFFFFF?text=' . $cast['name'],
                    'active'
                ]
            );
        }
    }
    
    // サンプルユーザーデータ
    $sample_users = [
        [
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'first_name' => '花子',
            'last_name' => '田中',
            'phone' => '090-1234-5678',
            'birth_date' => '1995-05-15',
            'gender' => 'female',
            'status' => 'active'
        ],
        [
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'first_name' => '太郎',
            'last_name' => '佐藤',
            'phone' => '090-2345-6789',
            'birth_date' => '1998-08-20',
            'gender' => 'male',
            'status' => 'active'
        ],
        [
            'username' => 'user3',
            'email' => 'user3@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'first_name' => '美咲',
            'last_name' => '鈴木',
            'phone' => '090-3456-7890',
            'birth_date' => '1996-12-10',
            'gender' => 'female',
            'status' => 'active'
        ]
    ];
    
    // ユーザーデータを挿入
    foreach ($sample_users as $user_data) {
        $db->query(
            "INSERT INTO users (username, email, password_hash, first_name, last_name, phone, birth_date, gender, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($user_data)
        );
    }
    
    // サンプル応募データ
    $user_ids = $db->fetchAll("SELECT id FROM users ORDER BY id DESC LIMIT 3");
    $job_ids = $db->fetchAll("SELECT id FROM jobs ORDER BY id DESC LIMIT 5");
    
    $sample_applications = [
        [
            'user_id' => $user_ids[0]['id'],
            'job_id' => $job_ids[0]['id'],
            'status' => 'pending',
            'message' => 'よろしくお願いします！'
        ],
        [
            'user_id' => $user_ids[1]['id'],
            'job_id' => $job_ids[1]['id'],
            'status' => 'pending',
            'message' => '未経験ですが、頑張ります！'
        ],
        [
            'user_id' => $user_ids[2]['id'],
            'job_id' => $job_ids[2]['id'],
            'status' => 'accepted',
            'message' => '面接の機会をいただき、ありがとうございます。'
        ]
    ];
    
    // 応募データを挿入
    foreach ($sample_applications as $app_data) {
        $db->query(
            "INSERT INTO applications (user_id, job_id, status, message) 
             VALUES (?, ?, ?, ?)",
            array_values($app_data)
        );
    }
    
    // サンプル口コミデータ
    $sample_reviews = [
        [
            'user_id' => $user_ids[0]['id'],
            'shop_id' => $shop_ids[0]['id'],
            'rating' => 5,
            'comment' => 'とても楽しい時間を過ごせました！スタッフの方が親切で、また来たいです。',
            'status' => 'approved'
        ],
        [
            'user_id' => $user_ids[1]['id'],
            'shop_id' => $shop_ids[1]['id'],
            'rating' => 4,
            'comment' => '雰囲気が良くて、料理も美味しかったです。',
            'status' => 'approved'
        ]
    ];
    
    // 口コミデータを挿入
    foreach ($sample_reviews as $review_data) {
        $db->query(
            "INSERT INTO reviews (user_id, shop_id, rating, comment, status) 
             VALUES (?, ?, ?, ?, ?)",
            array_values($review_data)
        );
    }
}

function insertPrefectureAndCityData() {
    global $db;
    
    // 都道府県データ（既存チェック）
    $prefectures = [
        ['id' => 13, 'name' => '東京都'],
        ['id' => 27, 'name' => '大阪府'],
        ['id' => 23, 'name' => '愛知県']
    ];
    
    foreach ($prefectures as $pref) {
        $exists = $db->fetch("SELECT id FROM prefectures WHERE id = ?", [$pref['id']]);
        if (!$exists) {
            $db->query("INSERT INTO prefectures (id, name) VALUES (?, ?)", [$pref['id'], $pref['name']]);
        }
    }
    
    // 市区町村データ（既存チェック）
    $cities = [
        ['id' => 1, 'prefecture_id' => 13, 'name' => '千代田区'],
        ['id' => 2, 'prefecture_id' => 13, 'name' => '渋谷区'],
        ['id' => 3, 'prefecture_id' => 13, 'name' => '新宿区'],
        ['id' => 4, 'prefecture_id' => 27, 'name' => '大阪市北区'],
        ['id' => 5, 'prefecture_id' => 23, 'name' => '名古屋市中区']
    ];
    
    foreach ($cities as $city) {
        $exists = $db->fetch("SELECT id FROM cities WHERE id = ?", [$city['id']]);
        if (!$exists) {
            $db->query("INSERT INTO cities (id, prefecture_id, name) VALUES (?, ?, ?)", 
                      [$city['id'], $city['prefecture_id'], $city['name']]);
        }
    }
}

function clearAllData() {
    global $db;
    
    // 外部キー制約を無視して削除
    $tables = ['applications', 'reviews', 'favorites', 'jobs', 'casts', 'shops', 'users'];
    
    foreach ($tables as $table) {
        $db->query("DELETE FROM $table");
        $db->query("ALTER TABLE $table AUTO_INCREMENT = 1");
    }
}

ob_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- 管理者ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>管理者パネル
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">
                    <i class="fas fa-database me-2"></i>サンプルデータ管理
                </h1>
            </div>
        </div>
        
        <!-- 成功・エラーメッセージ -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- 現在のデータ状況 -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>現在のデータ状況
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-primary"><?php echo $db->fetch("SELECT COUNT(*) as count FROM shops")['count']; ?></h3>
                                    <p class="mb-0">店舗数</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-success"><?php echo $db->fetch("SELECT COUNT(*) as count FROM jobs")['count']; ?></h3>
                                    <p class="mb-0">求人数</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-info"><?php echo $db->fetch("SELECT COUNT(*) as count FROM casts")['count']; ?></h3>
                                    <p class="mb-0">キャスト数</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-warning"><?php echo $db->fetch("SELECT COUNT(*) as count FROM users")['count']; ?></h3>
                                    <p class="mb-0">ユーザー数</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- サンプルデータ投入 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>サンプルデータ投入
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>以下のサンプルデータを投入します：</p>
                        <ul>
                            <li>都道府県・市区町村データ</li>
                            <li>店舗データ（5件）</li>
                            <li>求人データ（5件）</li>
                            <li>キャストデータ（5件）</li>
                            <li>ユーザーデータ（3件）</li>
                            <li>応募データ（3件）</li>
                            <li>口コミデータ（2件）</li>
                        </ul>
                        <form method="POST" onsubmit="return confirm('サンプルデータを投入しますか？')">
                            <input type="hidden" name="action" value="insert_sample_data">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download me-1"></i>サンプルデータを投入
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-trash-alt me-2"></i>データ全削除
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-danger"><strong>注意：</strong>すべてのデータが削除されます</p>
                        <ul>
                            <li>店舗データ</li>
                            <li>求人データ</li>
                            <li>キャストデータ</li>
                            <li>ユーザーデータ</li>
                            <li>応募データ</li>
                            <li>口コミデータ</li>
                        </ul>
                        <form method="POST" onsubmit="return confirm('本当にすべてのデータを削除しますか？この操作は取り消せません。')">
                            <input type="hidden" name="action" value="clear_all_data">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>すべてのデータを削除
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- サンプルデータの詳細 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>サンプルデータの詳細
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>店舗データ</h6>
                                <ul class="small">
                                    <li>メイドカフェ・ミアカフェ秋葉原店</li>
                                    <li>コンカフェ And Lovely</li>
                                    <li>執事喫茶 黒執事</li>
                                    <li>アイドルカフェ スターダスト</li>
                                    <li>コスプレカフェ コスモス</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6>求人データ</h6>
                                <ul class="small">
                                    <li>メイドカフェスタッフ募集</li>
                                    <li>コンカフェホールスタッフ</li>
                                    <li>執事喫茶スタッフ</li>
                                    <li>アイドルカフェパフォーマー</li>
                                    <li>コスプレカフェスタッフ</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6>キャストデータ</h6>
                                <ul class="small">
                                    <li>みおちゃん（22歳）</li>
                                    <li>あいりん（20歳）</li>
                                    <li>ゆきちゃん（24歳）</li>
                                    <li>まいちゃん（19歳）</li>
                                    <li>りんちゃん（21歳）</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$content = ob_get_clean();
echo $content;
?>
