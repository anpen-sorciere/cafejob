# 405/403エラーの解決方法

## 🔍 現在の状況

- `/cafejob/public/` → **405 Method Not Allowed** エラー
- `/cafejob/` → **403 Forbidden** エラー

## 📋 問題の分析

### 405エラーについて

405エラーは、Laravelが起動しているが、ルーティングに問題があることを示しています。

**原因の可能性:**
1. `RewriteBase` の設定が正しくない
2. ルーティングのパスが正しく認識されていない
3. HTTPメソッドの問題

### 403エラーについて

403エラーは、プロジェクトルートの `.htaccess` が正しく動作していないことを示しています。

**原因の可能性:**
1. `.htaccess` ファイルの権限が正しくない
2. `.htaccess` の内容に問題がある
3. mod_rewrite が有効でない

---

## 🔧 解決方法

### ステップ1: プロジェクトルートの `.htaccess` を修正

プロジェクトルートの `.htaccess` を以下の内容に更新しました：

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # publicディレクトリへのリダイレクト
    RewriteCond %{REQUEST_URI} !^/cafejob/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /cafejob/public/$1 [L]
</IfModule>

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

**FTPでアップロード:**
1. ローカルの `.htaccess` をサーバーの `/cafejob/.htaccess` にアップロード
2. 権限を **644** に設定

### ステップ2: `public/.htaccess` の `RewriteBase` を確認

`public/.htaccess` に `RewriteBase /cafejob/public/` が設定されていることを確認してください。

**現在の設定:**
```apache
RewriteBase /cafejob/public/
```

### ステップ3: 権限を設定

FTPクライアントで以下の権限を設定：

1. `/cafejob/.htaccess` → **644**
2. `/cafejob/public/.htaccess` → **644**
3. `/cafejob/public/` → **755**
4. `/cafejob/public/index.php` → **644**

### ステップ4: 動作確認

1. **まず `/cafejob/` にアクセス**
   - `https://purplelion51.sakura.ne.jp/cafejob/`
   - これで正常に表示されるはずです

2. **エラーが続く場合**
   - エラーログを確認
   - ブラウザの開発者ツール（F12）でネットワークタブを確認

---

## 🔍 追加の確認事項

### 1. エラーログを確認

さくらサーバーのコントロールパネルで：

1. 「ログ」→「エラーログ」を選択
2. 最新のエラーメッセージを確認
3. 405エラーや403エラーの詳細を確認

### 2. Laravelのログを確認

FTPで `storage/logs/laravel.log` をダウンロードして確認：

1. 最新のエラーメッセージを確認
2. ルーティングに関するエラーがないか確認

### 3. `.env` ファイルの設定を確認

サーバーの `.env` ファイルで以下を確認：

```env
APP_URL=https://purplelion51.sakura.ne.jp/cafejob
APP_DEBUG=false
APP_KEY=base64:...
```

---

## ✅ チェックリスト

以下を順番に確認・実行してください：

- [ ] プロジェクトルートの `.htaccess` を更新してアップロード
- [ ] `/cafejob/.htaccess` の権限が **644** に設定されている
- [ ] `/cafejob/public/.htaccess` に `RewriteBase /cafejob/public/` が設定されている
- [ ] `/cafejob/public/.htaccess` の権限が **644** に設定されている
- [ ] `/cafejob/public/` の権限が **755** に設定されている
- [ ] `/cafejob/public/index.php` の権限が **644** に設定されている
- [ ] `/cafejob/` にアクセスして動作確認

---

## 🎯 次のアクション

1. **修正した `.htaccess` をアップロード**
   - プロジェクトルートの `.htaccess` を更新

2. **権限を設定**
   - すべてのファイルとディレクトリの権限を確認

3. **動作確認**
   - `/cafejob/` にアクセスして確認

4. **エラーが続く場合**
   - エラーログを確認
   - Laravelのログを確認

