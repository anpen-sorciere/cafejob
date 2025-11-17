# DirectoryIndex エラーの解決方法

## 🔍 エラーの原因

エラーログから以下の問題が判明しました：

```
Cannot serve directory /home/purplelion51/www/cafejob/: No matching DirectoryIndex found
```

**原因:**
- `/cafejob/` にアクセスした際に、`DirectoryIndex`（index.phpなど）が見つからない
- ディレクトリの内容を表示しようとするが、`Options -Indexes` で禁止されている
- そのため、403エラーが発生

---

## 🔧 解決方法

### 修正した `.htaccess` の内容

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /cafejob/
    
    # DirectoryIndexを設定
    DirectoryIndex index.php
    
    # publicディレクトリへのリダイレクト
    RewriteCond %{REQUEST_URI} !^/cafejob/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
    
    # ルートアクセス時にpublic/index.phpにリダイレクト
    RewriteCond %{REQUEST_URI} ^/cafejob/?$
    RewriteRule ^$ public/index.php [L]
</IfModule>

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

**変更点:**
1. `DirectoryIndex index.php` を追加
2. ルートアクセス時のリダイレクトルールを追加

---

## 📋 次のステップ

### ステップ1: 修正した `.htaccess` をアップロード

FTPでサーバーの `/cafejob/.htaccess` にアップロードしてください。

または、SSHで直接編集：

```bash
cd /cafejob
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /cafejob/
    
    # DirectoryIndexを設定
    DirectoryIndex index.php
    
    # publicディレクトリへのリダイレクト
    RewriteCond %{REQUEST_URI} !^/cafejob/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
    
    # ルートアクセス時にpublic/index.phpにリダイレクト
    RewriteCond %{REQUEST_URI} ^/cafejob/?$
    RewriteRule ^$ public/index.php [L]
</IfModule>

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
EOF

chmod 644 .htaccess
```

### ステップ2: 動作確認

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/
```

正常に表示されるか確認してください。

---

## ✅ チェックリスト

- [ ] `.htaccess` に `DirectoryIndex index.php` を追加した
- [ ] ルートアクセス時のリダイレクトルールを追加した
- [ ] `.htaccess` の権限が **644** に設定されている
- [ ] `/cafejob/` にアクセスして動作確認した

---

## 🎯 期待される動作

修正後、以下のように動作するはずです：

1. `/cafejob/` にアクセス
   → `public/index.php` にリダイレクト
   → Laravelアプリケーションが表示される

2. `/cafejob/jobs` にアクセス
   → `public/jobs` にリダイレクト
   → Laravelのルーティングが処理される

