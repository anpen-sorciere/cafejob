<?php
// 超簡易版index.php（functions.phpなし）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

echo "<h1>カフェJob - 超簡易版</h1>";

try {
    // データベース接続
    echo "<h2>1. データベース接続</h2>";
    $host = 'mysql2103.db.sakura.ne.jp';
    $dbname = 'purplelion51_cafejob';
    $user = 'purplelion51';
    $pass = '-6r_am73';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ データベース接続成功<br>";
    
    // ログアウト処理
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // メインページの表示
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $allowed_pages = ['home', 'search', 'jobs', 'shops', 'cast', 'login', 'register', 'admin_login'];

    if (!in_array($page, $allowed_pages)) {
        $page = 'home';
    }

    echo "<h2>2. 表示するページ: $page</h2>";
    
    // ページファイルの存在確認
    $page_file = "pages/$page.php";
    if (file_exists($page_file)) {
        echo "✅ ページファイル存在: $page_file<br>";
        
        // ページファイルを読み込み
        echo "<h2>3. ページファイルの読み込み</h2>";
        
        if ($page === 'home') {
            // 簡易版ホームページ
            echo "<h3>ホームページ - 超簡易版</h3>";
            
            // 統計データの取得
            $total_jobs = $pdo->query("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'")->fetch()['count'];
            echo "✅ アクティブな求人数: $total_jobs<br>";
            
            $total_shops = $pdo->query("SELECT COUNT(*) as count FROM shops WHERE status = 'active'")->fetch()['count'];
            echo "✅ アクティブな店舗数: $total_shops<br>";
            
            $total_casts = $pdo->query("SELECT COUNT(*) as count FROM casts WHERE status = 'active'")->fetch()['count'];
            echo "✅ アクティブなキャスト数: $total_casts<br>";
            
            $total_applications = $pdo->query("SELECT COUNT(*) as count FROM applications")->fetch()['count'];
            echo "✅ 総応募数: $total_applications<br>";
            
            echo "<h3>✅ ホームページの処理が完了しました</h3>";
            
        } else {
            // 他のページは簡易表示
            echo "<h3>ページ: $page</h3>";
            echo "<p>このページは簡易版では表示されません。</p>";
        }
        
    } else {
        echo "❌ ページファイル不存在: $page_file<br>";
    }
    
    echo "<h2>✅ 超簡易版の処理が完了しました</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ エラーが発生しました</h2>";
    echo "<p style='color: red;'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>ファイル: " . $e->getFile() . "</p>";
    echo "<p>行: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>

