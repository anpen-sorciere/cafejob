<?php
// カフェJob 設定ファイル
// 環境自動判定版

// 環境判定
$is_production = (strpos($_SERVER['HTTP_HOST'], 'purplelion51.sakura.ne.jp') !== false);

if ($is_production) {
    // 本番環境設定
    define('DB_HOST', 'mysql2103.db.sakura.ne.jp');
    define('DB_NAME', 'purplelion51_cafejob');
    define('DB_USER', 'purplelion51');
    define('DB_PASS', '-6r_am73');
    define('SITE_URL', 'https://purplelion51.sakura.ne.jp/cafejob');
    define('DEBUG_MODE', false);
    define('LOG_LEVEL', 'ERROR');
} else {
    // 開発環境設定（XAMPP）
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'purplelion51_cafejob');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('SITE_URL', 'http://localhost/cafejob');
    define('DEBUG_MODE', true);
    define('LOG_LEVEL', 'INFO');
}

// 共通設定
define('SITE_NAME', 'カフェJob');
define('ADMIN_EMAIL', 'admin@cafejob.com');

// ファイルアップロード設定
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// ページネーション設定
define('ITEMS_PER_PAGE', 20);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// メール設定（SMTP）
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@cafejob.com');
define('SMTP_FROM_NAME', 'カフェJob');

// セキュリティ設定
define('SESSION_TIMEOUT', 3600); // 1時間
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15分

// 画像設定
define('MAX_IMAGE_WIDTH', 1920);
define('MAX_IMAGE_HEIGHT', 1080);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);

// 通知設定
define('ENABLE_EMAIL_NOTIFICATIONS', false);
define('ENABLE_PUSH_NOTIFICATIONS', false);

// キャッシュ設定
define('CACHE_ENABLED', $is_production); // 本番環境のみ有効
define('CACHE_DURATION', 3600); // 1時間

// ログ設定
define('LOG_ENABLED', true);
define('LOG_FILE', 'logs/app.log');

// API設定
define('API_ENABLED', false);
define('API_RATE_LIMIT', 100); // 1時間あたりのリクエスト数

// 外部サービス設定
define('GOOGLE_MAPS_API_KEY', '');
define('GOOGLE_ANALYTICS_ID', '');
define('FACEBOOK_APP_ID', '');
define('TWITTER_API_KEY', '');

// 決済設定（将来の拡張用）
define('PAYMENT_ENABLED', false);
define('STRIPE_PUBLIC_KEY', '');
define('STRIPE_SECRET_KEY', '');

// 多言語設定（将来の拡張用）
define('DEFAULT_LANGUAGE', 'ja');
define('SUPPORTED_LANGUAGES', ['ja', 'en']);

// モバイルアプリ設定（将来の拡張用）
define('MOBILE_APP_ENABLED', false);
define('FCM_SERVER_KEY', '');

// バックアップ設定
define('BACKUP_ENABLED', $is_production); // 本番環境のみ有効
define('BACKUP_SCHEDULE', 'daily'); // daily, weekly, monthly
define('BACKUP_RETENTION_DAYS', 30);

// 監視設定
define('MONITORING_ENABLED', $is_production); // 本番環境のみ有効
define('MONITORING_EMAIL', 'admin@cafejob.com');
define('MONITORING_THRESHOLD_ERRORS', 10);
define('MONITORING_THRESHOLD_RESPONSE_TIME', 5); // 秒

// 開発者向け設定
define('SHOW_DEBUG_INFO', DEBUG_MODE);
define('LOG_SQL_QUERIES', DEBUG_MODE);
define('DISPLAY_ERRORS', DEBUG_MODE);
define('ERROR_REPORTING', DEBUG_MODE ? E_ALL : 0);

// パフォーマンス設定
define('ENABLE_GZIP', true);
define('ENABLE_BROWSER_CACHING', true);
define('CACHE_STATIC_ASSETS', $is_production); // 本番環境のみ有効

// セキュリティヘッダー
define('ENABLE_SECURITY_HEADERS', true);
define('CSP_ENABLED', $is_production); // 本番環境のみ有効
define('HSTS_ENABLED', $is_production); // 本番環境のみ有効

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
define('PERFORMANCE_MONITORING', $is_production); // 本番環境のみ有効

// メンテナンスモード
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'システムメンテナンス中です。しばらくお待ちください。');

// バージョン情報
define('APP_VERSION', '1.0.0');
define('DB_VERSION', '1.0.0');
define('LAST_UPDATE', '2024-01-01');

// カスタム設定（必要に応じて追加）
define('CUSTOM_FEATURE_ENABLED', false);
define('CUSTOM_API_ENDPOINT', '');
define('CUSTOM_WEBHOOK_URL', '');

// 環境情報（デバッグ用）
define('ENVIRONMENT', $is_production ? 'production' : 'development');
?>