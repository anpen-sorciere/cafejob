# カフェJob XAMPP環境セットアップガイド

## 📋 環境情報

### XAMPP環境
- **データベース名**: purplelion51_cafejob
- **ユーザー**: root
- **パスワード**: （空）
- **URL**: http://localhost/cafejob

## 🚀 セットアップ手順

### ステップ1: XAMPPの起動確認

1. **XAMPPコントロールパネルを起動**
2. **ApacheとMySQLが起動していることを確認**

### ステップ2: データベースの作成

#### 2-1: phpMyAdminでデータベース作成
1. **ブラウザで `http://localhost/phpmyadmin` にアクセス**
2. **左側の「データベース」タブをクリック**
3. **データベース名**: `purplelion51_cafejob` を入力
4. **照合順序**: `utf8mb4_unicode_ci` を選択
5. **「作成」ボタンをクリック**

### ステップ3: スキーマのインポート

#### 3-1: 本番環境用スキーマの使用
1. **phpMyAdminで「purplelion51_cafejob」データベースを選択**
2. **「インポート」タブをクリック**
3. **`database/schema_production.sql`ファイルをアップロード**
4. **「実行」ボタンをクリック**

**注意**: `schema_production.sql`を使用することで、本番環境と同じデータベース構造になります。

### ステップ4: 動作確認

#### 4-1: メインサイトの確認
```
http://localhost/cafejob/
```
- ホームページが正常に表示されることを確認
- エラーが表示されないことを確認

#### 4-2: デモアカウントでのログイン確認
- **ユーザー名**: `demo_user`
- **パスワード**: `demo123`

#### 4-3: 管理者パネルの確認
```
http://localhost/cafejob/admin/
```
- **ユーザー名**: `admin`
- **パスワード**: `admin123`

## 🔧 設定ファイルの特徴

### 環境自動判定
`config/config.php`は環境を自動判定します：

- **開発環境（localhost）**: デバッグモード有効、詳細ログ
- **本番環境（purplelion51.sakura.ne.jp）**: デバッグモード無効、エラーログのみ

### データベース設定
```php
// 開発環境
define('DB_HOST', 'localhost');
define('DB_NAME', 'purplelion51_cafejob');
define('DB_USER', 'root');
define('DB_PASS', '');

// 本番環境（自動判定）
define('DB_HOST', 'mysql2103.db.sakura.ne.jp');
define('DB_NAME', 'purplelion51_cafejob');
define('DB_USER', 'purplelion51');
define('DB_PASS', '-6r_am73');
```

## 🛠️ 開発時の注意点

### 1. データベース名の統一
- 開発環境と本番環境で同じデータベース名（`purplelion51_cafejob`）を使用
- データベース構造の一貫性を保つ

### 2. 設定ファイルの管理
- `config/config.php`は環境自動判定機能付き
- 手動で環境を切り替える必要がない

### 3. デバッグ機能
- 開発環境では詳細なエラー情報が表示される
- 本番環境ではエラーログのみ記録

## 🚨 トラブルシューティング

### よくある問題

#### データベース接続エラー
- XAMPPのMySQLが起動しているか確認
- データベース名が`purplelion51_cafejob`になっているか確認

#### ページが表示されない
- Apacheが起動しているか確認
- PHPのエラーログを確認

#### インポートエラー
- `schema_production.sql`を使用しているか確認
- データベースの文字コードが`utf8mb4_unicode_ci`になっているか確認

## 📊 開発環境での機能

### デバッグ機能
- SQLクエリのログ出力
- 詳細なエラー情報表示
- パフォーマンス情報表示

### 開発支援
- ホットリロード機能
- リアルタイムエラー表示
- データベース操作の可視化

---

**XAMPP環境でのセットアップが完了しました！**



