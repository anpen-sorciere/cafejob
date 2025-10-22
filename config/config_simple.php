<?php
// 本番環境用簡易設定ファイル
// Internal Server Error の原因を特定するため

// エラー表示を有効にする（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// データベース設定
define('DB_HOST', 'mysql2103.db.sakura.ne.jp');
define('DB_NAME', 'purplelion51_cafejob');
define('DB_USER', 'purplelion51');
define('DB_PASS', '-6r_am73');

// サイト設定
define('SITE_NAME', 'カフェJob');
define('SITE_URL', 'https://purplelion51.sakura.ne.jp/cafejob');
define('ADMIN_EMAIL', 'admin@cafejob.com');

// ファイルアップロード設定
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// ページネーション設定
define('ITEMS_PER_PAGE', 20);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// デバッグモード（本番環境では false）
define('DEBUG_MODE', true);

// その他の設定
define('SESSION_TIMEOUT', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);

// 画像設定
define('MAX_IMAGE_WIDTH', 1920);
define('MAX_IMAGE_HEIGHT', 1080);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);

// 通知設定
define('ENABLE_EMAIL_NOTIFICATIONS', false);
define('ENABLE_PUSH_NOTIFICATIONS', false);

// キャッシュ設定
define('CACHE_ENABLED', false);
define('CACHE_DURATION', 3600);

// ログ設定
define('LOG_ENABLED', true);
define('LOG_FILE', 'logs/app.log');
define('LOG_LEVEL', 'DEBUG');

// API設定
define('API_ENABLED', false);
define('API_RATE_LIMIT', 100);

// 外部サービス設定
define('GOOGLE_MAPS_API_KEY', '');
define('GOOGLE_ANALYTICS_ID', '');
define('FACEBOOK_APP_ID', '');
define('TWITTER_API_KEY', '');

// 決済設定
define('PAYMENT_ENABLED', false);
define('STRIPE_PUBLIC_KEY', '');
define('STRIPE_SECRET_KEY', '');

// 多言語設定
define('DEFAULT_LANGUAGE', 'ja');
define('SUPPORTED_LANGUAGES', ['ja', 'en']);

// モバイルアプリ設定
define('MOBILE_APP_ENABLED', false);
define('FCM_SERVER_KEY', '');

// バックアップ設定
define('BACKUP_ENABLED', false);
define('BACKUP_SCHEDULE', 'daily');
define('BACKUP_RETENTION_DAYS', 30);

// 監視設定
define('MONITORING_ENABLED', false);
define('MONITORING_EMAIL', 'admin@cafejob.com');
define('MONITORING_THRESHOLD_ERRORS', 10);
define('MONITORING_THRESHOLD_RESPONSE_TIME', 5);

// 開発者向け設定
define('SHOW_DEBUG_INFO', DEBUG_MODE);
define('LOG_SQL_QUERIES', DEBUG_MODE);
define('DISPLAY_ERRORS', DEBUG_MODE);
define('ERROR_REPORTING', DEBUG_MODE ? E_ALL : 0);

// パフォーマンス設定
define('ENABLE_GZIP', true);
define('ENABLE_BROWSER_CACHING', true);
define('CACHE_STATIC_ASSETS', false);

// セキュリティヘッダー
define('ENABLE_SECURITY_HEADERS', false);
define('CSP_ENABLED', false);
define('HSTS_ENABLED', false);

// ファイル権限設定
define('FILE_PERMISSIONS', 0644);
define('DIRECTORY_PERMISSIONS', 0755);

// アップロード制限
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
define('MAX_FILES_PER_UPLOAD', 5);

// 検索設定
define('SEARCH_RESULTS_PER_PAGE', 20);
define('SEARCH_HIGHLIGHT_ENABLED', true);
define('SEARCH_FUZZY_MATCHING', true);

// 通知設定
define('NOTIFICATION_TYPES', [
    'new_job' => true,
    'application_update' => true,
    'review_approved' => true,
    'shop_approved' => true
]);

// 統計設定
define('ANALYTICS_ENABLED', true);
define('USER_TRACKING_ENABLED', false);
define('PERFORMANCE_MONITORING', false);

// メンテナンスモード
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'システムメンテナンス中です。しばらくお待ちください。');

// バージョン情報
define('APP_VERSION', '1.0.0');
define('DB_VERSION', '1.0.0');
define('LAST_UPDATE', '2024-01-01');

// カスタム設定
define('CUSTOM_FEATURE_ENABLED', false);
define('CUSTOM_API_ENDPOINT', '');
define('CUSTOM_WEBHOOK_URL', '');

// 環境情報
define('ENVIRONMENT', 'production');
?>




