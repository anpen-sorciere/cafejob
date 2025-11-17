# .htaccess の最終修正手順

## ✅ 現在の状況

- `/cafejob/public/index.php` → **正常に表示される** ✅
- `/cafejob/` → **403 Forbidden エラー** ❌

## 🔍 問題の原因

プロジェクトルートの `.htaccess` が正しく動作していない可能性があります。

---

## 🔧 解決方法

### ステップ1: `.htaccess` ファイルの存在を確認

SSHで以下を実行：

```bash
cd /cafejob
ls -la .htaccess
```

または、FTPクライアントで `/cafejob/.htaccess` が存在するか確認してください。

### ステップ2: `.htaccess` の内容を確認

SSHで以下を実行：

```bash
cat .htaccess
```

正しい内容は以下の通りです：

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

### ステップ3: `.htaccess` が存在しない場合

`.htaccess.backup` を `.htaccess` にリネーム：

```bash
mv .htaccess.backup .htaccess
chmod 644 .htaccess
```

### ステップ4: `.htaccess` の権限を確認

```bash
chmod 644 .htaccess
ls -la .htaccess
```

権限が `644` になっていることを確認してください。

### ステップ5: `.htaccess` の内容が正しくない場合

正しい内容で `.htaccess` を作成：

```bash
cat > .htaccess << 'EOF'
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
EOF

chmod 644 .htaccess
```

---

## 🔍 追加の確認事項

### 1. さくらサーバーの設定を確認

さくらサーバーのコントロールパネルで：

1. **「サーバーの設定」→「Apache設定」を確認**
   - `.htaccess` の使用が有効になっているか確認

2. **「ログ」→「エラーログ」を確認**
   - 403エラーの詳細な原因を確認

### 2. テストファイルで確認

`.htaccess` が動作しているか確認するため、テストファイルを作成：

```bash
echo "test" > test.txt
chmod 644 test.txt
```

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/test.txt
```

- ファイルが表示される → `.htaccess` は動作している
- 403エラー → `.htaccess` または権限の問題

---

## ✅ チェックリスト

- [ ] `/cafejob/.htaccess` ファイルが存在する
- [ ] `.htaccess` の内容が正しい
- [ ] `.htaccess` の権限が **644** に設定されている
- [ ] さくらサーバーの設定で `.htaccess` が有効になっている
- [ ] `/cafejob/` にアクセスして動作確認

---

## 🎯 次のアクション

1. **`.htaccess` ファイルの存在を確認**
2. **`.htaccess` の内容を確認**
3. **権限を設定**
4. **動作確認**

---

## 📞 それでも解決しない場合

さくらサーバーのコントロールパネルで：

1. **「サーバーの設定」→「サブディレクトリ」を確認**
   - `/cafejob/` の設定を確認

2. **サポートに問い合わせ**
   - サブディレクトリで `.htaccess` が動作しない原因を確認

