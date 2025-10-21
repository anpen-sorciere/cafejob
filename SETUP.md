# カフェJob セットアップガイド

## 1. 環境準備

### 必要なソフトウェア
- PHP 8.0以上
- MySQL 8.0以上
- Apache/Nginx Webサーバー
- Composer（推奨）

### XAMPPを使用する場合
1. XAMPPをダウンロード・インストール
2. ApacheとMySQLを起動
3. `htdocs`フォルダにプロジェクトを配置

## 2. プロジェクトのセットアップ

### ステップ1: ファイルの配置
```bash
# XAMPPの場合
C:\xampp\htdocs\cafejob\

# または任意のWebサーバーのドキュメントルートに配置
```

### ステップ2: データベースの作成
1. phpMyAdminにアクセス（http://localhost/phpmyadmin）
2. 新しいデータベース「cafejob」を作成
3. `database/schema.sql`をインポート

または、コマンドラインから：
```bash
mysql -u root -p < database/schema.sql
```

### ステップ3: 設定ファイルの作成
```bash
# config.php.exampleをコピー
cp config/config.php.example config/config.php

# 設定ファイルを編集
# データベース接続情報を正しく設定
```

### ステップ4: ディレクトリ権限の設定
```bash
# アップロードディレクトリの作成と権限設定
mkdir uploads
chmod 755 uploads/
chmod 755 assets/
```

## 3. 設定の確認

### config/config.php の設定例
```php
// データベース設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'cafejob');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPPのデフォルトは空

// サイト設定
define('SITE_NAME', 'カフェJob');
define('SITE_URL', 'http://localhost/cafejob');
```

## 4. 動作確認

### アクセスURL
- メインサイト: http://localhost/cafejob/
- 管理者パネル: http://localhost/cafejob/admin/

### デモアカウント
**一般ユーザー:**
- ユーザー名: demo_user
- パスワード: demo123

**管理者:**
- ユーザー名: admin
- パスワード: admin123

## 5. トラブルシューティング

### よくある問題

#### データベース接続エラー
- MySQLが起動しているか確認
- データベース名、ユーザー名、パスワードが正しいか確認
- `config/config.php`の設定を確認

#### ファイルアップロードエラー
- `uploads/`ディレクトリが存在するか確認
- ディレクトリの権限が755になっているか確認
- PHPの`upload_max_filesize`設定を確認

#### ページが表示されない
- Apacheが起動しているか確認
- `.htaccess`ファイルが正しく配置されているか確認
- PHPのエラーログを確認

### ログの確認
```bash
# Apacheエラーログ
tail -f /var/log/apache2/error.log

# PHPエラーログ
tail -f /var/log/php_errors.log
```

## 6. 本番環境へのデプロイ

### セキュリティ設定
1. `config/config.php`で`DEBUG_MODE`を`false`に設定
2. データベースパスワードを強力なものに変更
3. 管理者パスワードを変更
4. `uploads/`ディレクトリの権限を適切に設定

### パフォーマンス最適化
1. PHPのOPcacheを有効化
2. MySQLのクエリキャッシュを有効化
3. 静的ファイルのキャッシュ設定
4. Gzip圧縮を有効化

## 7. バックアップ

### データベースのバックアップ
```bash
mysqldump -u root -p cafejob > backup_$(date +%Y%m%d).sql
```

### ファイルのバックアップ
```bash
tar -czf cafejob_backup_$(date +%Y%m%d).tar.gz /path/to/cafejob/
```

## 8. 更新・メンテナンス

### 定期的なメンテナンス
- ログファイルのローテーション
- データベースの最適化
- 不要なファイルの削除
- セキュリティアップデートの適用

### バージョンアップ
1. 現在のバージョンをバックアップ
2. 新しいバージョンをダウンロード
3. データベースマイグレーションを実行
4. 設定ファイルを更新
5. 動作確認

## 9. サポート

### ドキュメント
- README.md: 基本的な使用方法
- このファイル: セットアップガイド
- 各PHPファイル内のコメント: 詳細な説明

### ヘルプ
- GitHub Issues: バグ報告・機能要望
- メール: admin@cafejob.com（設定で変更可能）

## 10. ライセンス

このプロジェクトはMITライセンスの下で公開されています。
詳細はLICENSEファイルを参照してください。

