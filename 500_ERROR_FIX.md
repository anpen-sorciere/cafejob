# 500エラーの解決方法

## ✅ 進展

- PHPは動作している
- Laravelは起動しようとしている（500エラー）
- プロジェクトルートの `.htaccess` は問題ではない

## 🔍 500エラーの原因

500エラーは、Laravelが起動しようとしているが、何かエラーが発生していることを示しています。

**よくある原因:**
1. **`.env` ファイルの設定ミス**
   - `APP_KEY` が設定されていない
   - データベース接続エラー

2. **ディレクトリの権限問題**
   - `storage` ディレクトリの権限が不足
   - `bootstrap/cache` ディレクトリの権限が不足

3. **ログファイルが作成できない**
   - `storage/logs` ディレクトリの権限が不足

4. **vendorディレクトリの問題**
   - `vendor` ディレクトリが正しくアップロードされていない

---

## 🔧 解決方法

### ステップ1: ログファイルを確認

まず、ログファイルが作成されているか確認：

1. **FTPで `/cafejob/storage/logs/` ディレクトリを確認**
   - `laravel.log` ファイルが存在するか確認
   - 存在する場合は、ダウンロードしてエラーメッセージを確認

2. **ログファイルが存在しない場合**
   - `storage/logs` ディレクトリの権限を **775** に設定
   - 再度アクセスして、ログファイルが作成されるか確認

### ステップ2: ディレクトリの権限を設定

FTPクライアントで以下の権限を設定：

**必須の権限設定:**
- `/cafejob/storage/` → **775**
- `/cafejob/storage/logs/` → **775**
- `/cafejob/storage/framework/` → **775**
- `/cafejob/storage/framework/cache/` → **775**
- `/cafejob/storage/framework/sessions/` → **775**
- `/cafejob/storage/framework/views/` → **775**
- `/cafejob/bootstrap/cache/` → **775**

### ステップ3: `.env` ファイルの設定を確認

サーバーの `.env` ファイルで以下を確認：

```env
APP_KEY=base64:HFxAIZT+BKITFDNKTxWdu1K+I0pJEZG/BT3LqCUZ8FQ=
APP_DEBUG=true  # 一時的にtrueに設定して詳細なエラーを確認
APP_URL=https://purplelion51.sakura.ne.jp/cafejob

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=データベース名
DB_USERNAME=ユーザー名
DB_PASSWORD=パスワード
```

**重要**: 
- `APP_KEY` が設定されているか確認
- 一時的に `APP_DEBUG=true` に設定して、詳細なエラーを確認

### ステップ4: エラーログを確認

さくらサーバーのコントロールパネルで：

1. **「ログ」→「エラーログ」を選択**
2. **最新のエラーメッセージを確認**
3. **500エラーの詳細な原因を確認**

---

## 🎯 推奨される手順

### 1. まず権限を設定

```
storage/ → 775
storage/logs/ → 775
storage/framework/ → 775
storage/framework/cache/ → 775
storage/framework/sessions/ → 775
storage/framework/views/ → 775
bootstrap/cache/ → 775
```

### 2. `.env` ファイルを確認

- `APP_KEY` が設定されているか確認
- 一時的に `APP_DEBUG=true` に設定

### 3. ログファイルを確認

- `storage/logs/laravel.log` をダウンロード
- エラーメッセージを確認

### 4. 再度アクセス

- `/cafejob/public/index.php` にアクセス
- エラーメッセージを確認

---

## 📋 チェックリスト

- [ ] `storage/` ディレクトリの権限が **775** に設定されている
- [ ] `storage/logs/` ディレクトリの権限が **775** に設定されている
- [ ] `storage/framework/` ディレクトリの権限が **775** に設定されている
- [ ] `bootstrap/cache/` ディレクトリの権限が **775** に設定されている
- [ ] `.env` ファイルの `APP_KEY` が設定されている
- [ ] `.env` ファイルの `APP_DEBUG=true` に設定（一時的）
- [ ] `storage/logs/laravel.log` を確認
- [ ] エラーログを確認

