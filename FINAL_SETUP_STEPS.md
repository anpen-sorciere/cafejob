# 最終設定と動作確認の手順

## ✅ 完了した作業

- `vendor` ディレクトリが作成された
- 必要なパッケージがインストールされた
- `vendor/autoload.php` が存在する

## 🔧 次のステップ

### ステップ1: 権限を設定

SSHで以下のコマンドを実行：

```bash
chmod -R 755 vendor
chmod 644 vendor/autoload.php
chmod -R 775 storage bootstrap/cache
chmod 755 public
chmod 644 public/index.php
chmod 644 public/.htaccess
```

### ステップ2: プロジェクトルートの `.htaccess` を復元

FTPクライアントで：

1. **サーバーの `/cafejob/.htaccess.backup` を `.htaccess` にリネーム**
   - `.htaccess.backup` を右クリック → 「名前の変更」→ `.htaccess`

または、SSHで：

```bash
cd /cafejob
mv .htaccess.backup .htaccess
chmod 644 .htaccess
```

### ステップ3: 動作確認

ブラウザで以下にアクセス：

1. **`/cafejob/public/index.php` に直接アクセス**
   ```
   https://purplelion51.sakura.ne.jp/cafejob/public/index.php
   ```
   - Laravelが起動するか確認

2. **`/cafejob/` にアクセス（`.htaccess` を復元後）**
   ```
   https://purplelion51.sakura.ne.jp/cafejob/
   ```
   - 正常に表示されるか確認

---

## 🔍 トラブルシューティング

### 500エラーが続く場合

1. **ログファイルを確認**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   または、FTPで `storage/logs/laravel.log` をダウンロード

2. **`.env` ファイルの設定を確認**
   - `APP_KEY` が設定されているか確認
   - `APP_DEBUG=true` に一時的に設定して詳細なエラーを確認

3. **ディレクトリの権限を再確認**
   ```bash
   ls -la storage/
   ls -la bootstrap/cache/
   ```

### 403エラーが続く場合

1. **`.htaccess` ファイルの権限を確認**
   ```bash
   ls -la .htaccess
   ls -la public/.htaccess
   ```

2. **権限を設定**
   ```bash
   chmod 644 .htaccess
   chmod 644 public/.htaccess
   ```

### データベース接続エラーが表示される場合

1. **`.env` ファイルのデータベース設定を確認**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=データベース名
   DB_USERNAME=ユーザー名
   DB_PASSWORD=パスワード
   ```

2. **データベースマイグレーションを実行**
   ```bash
   php artisan migrate --force
   ```

---

## ✅ チェックリスト

- [ ] `vendor` ディレクトリの権限が **755** に設定されている
- [ ] `vendor/autoload.php` の権限が **644** に設定されている
- [ ] `storage` ディレクトリの権限が **775** に設定されている
- [ ] `bootstrap/cache` ディレクトリの権限が **775** に設定されている
- [ ] `public` ディレクトリの権限が **755** に設定されている
- [ ] `public/index.php` の権限が **644** に設定されている
- [ ] プロジェクトルートの `.htaccess` が復元されている
- [ ] `/cafejob/public/index.php` にアクセスして動作確認
- [ ] `/cafejob/` にアクセスして動作確認

---

## 🎯 次のアクション

1. **権限を設定**
   - 上記のコマンドを実行

2. **`.htaccess` を復元**
   - FTPまたはSSHで復元

3. **動作確認**
   - ブラウザでアクセスして確認

4. **エラーが続く場合**
   - ログファイルを確認
   - エラーメッセージを確認

