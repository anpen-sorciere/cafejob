# カフェJob 本番環境デプロイメントガイド

## 📋 本番環境情報

### サーバー情報
- **ホスト**: mysql2103.db.sakura.ne.jp
- **データベース**: purplelion51_cafejob
- **ユーザー**: purplelion51
- **URL**: https://purplelion51.sakura.ne.jp/cafejob

## 🚀 デプロイメント手順

### ステップ1: ファイルのアップロード

1. **FTP/SFTPクライアントでサーバーに接続**
2. **`/cafejob/`ディレクトリに以下のファイルをアップロード**：
   ```
   cafejob/
   ├── index.php
   ├── config/
   │   ├── config.php (本番環境用に更新済み)
   │   └── database.php
   ├── database/
   │   └── schema.sql
   ├── includes/
   │   ├── layout.php
   │   └── functions.php
   ├── pages/
   │   ├── home.php
   │   ├── jobs.php
   │   ├── shops.php
   │   ├── login.php
   │   ├── register.php
   │   └── admin_login.php
   ├── admin/
   │   ├── index.php
   │   ├── logout.php
   │   ├── includes.php
   │   └── layout.php
   ├── assets/
   │   ├── css/
   │   │   └── style.css
   │   ├── js/
   │   │   └── main.js
   │   └── images/
   ├── uploads/ (空ディレクトリ)
   └── logs/ (空ディレクトリ)
   ```

### ステップ2: ディレクトリ権限の設定

サーバー上で以下の権限を設定：

```bash
# アップロードディレクトリ
chmod 755 uploads/
chmod 755 logs/

# 設定ファイル（セキュリティのため）
chmod 644 config/config.php
chmod 644 config/database.php

# その他のファイル
chmod 644 *.php
chmod 644 includes/*.php
chmod 644 pages/*.php
chmod 644 admin/*.php
```

### ステップ3: データベースのセットアップ

#### 3-1: phpMyAdminでのデータベース作成
1. **サーバーのphpMyAdminにアクセス**
2. **データベース「purplelion51_cafejob」が存在することを確認**
3. **存在しない場合は作成**

#### 3-2: スキーマのインポート
1. **phpMyAdminで「purplelion51_cafejob」データベースを選択**
2. **「インポート」タブをクリック**
3. **`database/schema_production.sql`ファイルをアップロード**
4. **「実行」ボタンをクリック**

**重要**: 本番環境では`schema_production.sql`を使用してください。このファイルは`CREATE DATABASE`文を含まないため、既存のデータベースに安全にインポートできます。

### ステップ4: 動作確認

#### 4-1: メインサイトの確認
```
https://purplelion51.sakura.ne.jp/cafejob/
```
- ホームページが正常に表示されることを確認
- エラーが表示されないことを確認

#### 4-2: データベース接続の確認
- ページが正常に表示されれば、データベース接続は成功

#### 4-3: デモアカウントでのログイン確認
- **ユーザー名**: `demo_user`
- **パスワード**: `demo123`

#### 4-4: 管理者パネルの確認
```
https://purplelion51.sakura.ne.jp/cafejob/admin/
```
- **ユーザー名**: `admin`
- **パスワード**: `admin123`

## 🔧 本番環境での設定変更点

### データベース設定
```php
define('DB_HOST', 'mysql2103.db.sakura.ne.jp');
define('DB_NAME', 'purplelion51_cafejob');
define('DB_USER', 'purplelion51');
define('DB_PASS', '-6r_am73');
```

### サイトURL
```php
define('SITE_URL', 'https://purplelion51.sakura.ne.jp/cafejob');
```

### セキュリティ設定
- **デバッグモード**: `false`（本番環境）
- **ログレベル**: `ERROR`（エラーのみ記録）
- **キャッシュ**: `true`（パフォーマンス向上）
- **バックアップ**: `true`（データ保護）

## 🛡️ セキュリティ対策

### 1. ファイル権限
- 設定ファイル: `644`
- 実行ファイル: `644`
- ディレクトリ: `755`
- アップロードディレクトリ: `755`

### 2. .htaccessファイル（推奨）
```apache
# セキュリティヘッダー
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# ディレクトリリスティング無効
Options -Indexes

# 設定ファイルへの直接アクセス禁止
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

# ログファイルへの直接アクセス禁止
<Files "*.log">
    Order Allow,Deny
    Deny from all
</Files>
```

### 3. パスワードの変更
本番環境では、デモアカウントのパスワードを変更することを強く推奨：

```sql
-- 管理者パスワードの変更
UPDATE admins SET password_hash = '$2y$10$新しいハッシュ' WHERE username = 'admin';

-- デモユーザーパスワードの変更
UPDATE users SET password_hash = '$2y$10$新しいハッシュ' WHERE username = 'demo_user';
```

## 📊 監視・メンテナンス

### 1. ログ監視
- `logs/app.log`でエラーログを確認
- 定期的にログファイルをローテーション

### 2. データベースバックアップ
- 定期的なデータベースバックアップの設定
- バックアップファイルの安全な保管

### 3. パフォーマンス監視
- ページ読み込み速度の監視
- データベースクエリの最適化

## 🚨 トラブルシューティング

### よくある問題

#### データベース接続エラー
- ホスト名、ユーザー名、パスワード、データベース名を確認
- サーバーのMySQLサービスが起動しているか確認

#### ファイル権限エラー
- アップロードディレクトリの権限を確認
- ログディレクトリの権限を確認

#### ページが表示されない
- PHPのエラーログを確認
- ファイルのアップロードが完了しているか確認

#### セッションエラー
- セッション保存ディレクトリの権限を確認
- PHPのセッション設定を確認

## 📞 サポート

問題が発生した場合：
1. エラーログを確認
2. データベース接続を確認
3. ファイル権限を確認
4. 必要に応じてサポートに連絡

---

**本番環境へのデプロイが完了しました！**
