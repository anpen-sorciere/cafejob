# カフェJob - コンカフェ専門求人・集客サイト

[カフェるん](https://caferun.jp/)を参考にしたコンカフェ専門の求人・集客サイトです。

## 機能

### ユーザー機能
- **求人検索**: エリア、コンセプト、給与などで絞り込み検索
- **店舗検索**: コンセプト、制服タイプなどで店舗を検索
- **キャスト検索**: 人気キャストの検索・ランキング
- **応募機能**: 求人への応募と応募履歴管理
- **お気に入り**: 求人・店舗のお気に入り登録
- **口コミ**: 店舗への口コミ投稿・評価

### 管理者機能
- **店舗管理**: 店舗の承認・編集・削除
- **求人管理**: 求人情報の管理
- **ユーザー管理**: ユーザーアカウントの管理
- **応募管理**: 応募状況の確認・管理
- **口コミ管理**: 口コミの承認・管理
- **統計ダッシュボード**: サイトの利用状況確認

### 店舗管理者機能
- **求人投稿**: 求人情報の投稿・編集
- **キャスト管理**: キャスト情報の管理
- **応募確認**: 自店舗への応募確認
- **店舗情報管理**: 店舗情報の更新

## 技術仕様

- **フロントエンド**: HTML5, CSS3, JavaScript, Bootstrap 5
- **バックエンド**: PHP 8.0+
- **データベース**: MySQL 8.0+
- **レスポンシブ**: PC・スマートフォン両対応

## セットアップ

### 1. 環境要件
- PHP 8.0以上
- MySQL 8.0以上
- Apache/Nginx Webサーバー
- Composer（推奨）

### 2. インストール

```bash
# プロジェクトをクローン
git clone [repository-url] cafejob
cd cafejob

# データベースの作成
mysql -u root -p < database/schema.sql

# 設定ファイルの編集
cp config/config.php.example config/config.php
# config/config.php を編集してデータベース接続情報を設定
```

### 3. 設定

`config/config.php` で以下の設定を行ってください：

```php
// データベース設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'cafejob');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');

// サイト設定
define('SITE_NAME', 'カフェJob');
define('SITE_URL', 'http://localhost/cafejob');
```

### 4. ディレクトリ権限

```bash
# アップロードディレクトリの権限設定
chmod 755 uploads/
chmod 755 assets/
```

## デモアカウント

### 一般ユーザー
- **ユーザー名**: demo_user
- **パスワード**: demo123

### 管理者
- **ユーザー名**: admin
- **パスワード**: admin123

## ディレクトリ構造

```
cafejob/
├── admin/                 # 管理者パネル
├── api/                   # API エンドポイント
├── assets/                # 静的ファイル
│   ├── css/              # CSS ファイル
│   ├── js/               # JavaScript ファイル
│   └── images/           # 画像ファイル
├── config/                # 設定ファイル
├── database/              # データベース関連
├── includes/              # 共通ファイル
├── pages/                 # ページファイル
├── uploads/               # アップロードファイル
├── index.php              # メインファイル
└── README.md              # このファイル
```

## 主要ページ

### ユーザー向け
- `/` - ホームページ
- `/?page=jobs` - 求人検索
- `/?page=shops` - 店舗検索
- `/?page=cast` - キャスト検索
- `/?page=login` - ログイン
- `/?page=register` - 新規登録

### 管理者向け
- `/admin/` - 管理者ダッシュボード
- `/admin/shops.php` - 店舗管理
- `/admin/jobs.php` - 求人管理
- `/admin/users.php` - ユーザー管理
- `/admin/applications.php` - 応募管理

## データベース設計

### 主要テーブル
- `users` - ユーザー情報
- `shops` - 店舗情報
- `jobs` - 求人情報
- `casts` - キャスト情報
- `applications` - 応募情報
- `reviews` - 口コミ情報
- `admins` - 管理者情報

## セキュリティ機能

- パスワードハッシュ化
- SQLインジェクション対策
- XSS対策
- CSRF対策
- セッション管理
- 入力値検証

## レスポンシブデザイン

- Bootstrap 5を使用
- モバイルファーストデザイン
- タブレット・スマートフォン対応
- タッチフレンドリーなUI

## 今後の拡張予定

- [ ] メール通知機能
- [ ] チャット機能
- [ ] 決済機能
- [ ] 多言語対応
- [ ] API提供
- [ ] モバイルアプリ

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## サポート

ご質問やサポートが必要な場合は、GitHubのIssuesページでお知らせください。



