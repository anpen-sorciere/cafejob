# 404エラーの解決方法

## 🔍 現在の状況

- `/cafejob/` → **404エラー** ❌
- `/cafejob/public/index.php` → **正常に表示される** ✅

## 📋 問題の原因

404エラーは、リダイレクトは動作しているが、正しいパスに到達していない可能性があります。

**考えられる原因:**
1. リライトルールの順序が正しくない
2. リライト条件が正しく動作していない
3. `RewriteBase` の設定が正しくない

---

## 🔧 解決方法

### 修正した `.htaccess` の内容

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /cafejob/
    
    # 既にpublicディレクトリにいる場合は何もしない
    RewriteCond %{REQUEST_URI} ^/cafejob/public/
    RewriteRule ^ - [L]
    
    # ファイルやディレクトリが存在する場合は何もしない
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # publicディレクトリへのリダイレクト
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

**変更点:**
1. `DirectoryIndex` を削除（`public/.htaccess` で処理される）
2. リライトルールの順序を変更
3. より確実なリダイレクトルールに変更

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
    
    # 既にpublicディレクトリにいる場合は何もしない
    RewriteCond %{REQUEST_URI} ^/cafejob/public/
    RewriteRule ^ - [L]
    
    # ファイルやディレクトリが存在する場合は何もしない
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # publicディレクトリへのリダイレクト
    RewriteRule ^(.*)$ public/$1 [L]
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

## 🔍 追加の確認事項

### 1. エラーログを確認

さくらサーバーのコントロールパネルで：

1. **「ログ」→「エラーログ」を選択**
2. **最新のエラーメッセージを確認**
3. **404エラーの詳細な原因を確認**

### 2. `public/.htaccess` の確認

`public/.htaccess` に `RewriteBase /cafejob/public/` が設定されているか確認：

```bash
cat public/.htaccess
```

---

## ✅ チェックリスト

- [ ] `.htaccess` の内容を修正した
- [ ] `.htaccess` の権限が **644** に設定されている
- [ ] `/cafejob/` にアクセスして動作確認した
- [ ] エラーログを確認した

---

## 🎯 期待される動作

修正後、以下のように動作するはずです：

1. `/cafejob/` にアクセス
   → `public/` にリダイレクト
   → `public/index.php` が処理される
   → Laravelアプリケーションが表示される

2. `/cafejob/jobs` にアクセス
   → `public/jobs` にリダイレクト
   → Laravelのルーティングが処理される

