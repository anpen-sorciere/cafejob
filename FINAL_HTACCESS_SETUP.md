# 最終的な.htaccess設定

## ✅ baseball_slgの.htaccessの内容

`baseball_slg`の`.htaccess`の内容が確認できました。cafejobプロジェクトも完全に同じ内容にする必要があります。

---

## 🔧 cafejobプロジェクトの.htaccessを設定

### ステップ1: .htaccessを再作成

SSHで以下を実行：

```bash
cd /home/purplelion51/www/cafejob
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

chmod 644 .htaccess
```

**重要**: `.env`ファイルの保護の部分は含めていません。`baseball_slg`にも含まれていないためです。

### ステップ2: .htaccessの内容を確認

```bash
cat .htaccess
```

`baseball_slg`と完全に同じ内容になっているか確認してください。

### ステップ3: 比較

```bash
diff .htaccess /home/purplelion51/www/baseball_slg/.htaccess
```

違いがなければ、何も表示されません。

### ステップ4: index.phpのパスを確認

```bash
cat index.php | grep -E "(vendor|bootstrap|storage)"
```

以下のように表示されるはずです：

```
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

`../`が含まれていないことを確認してください。

### ステップ5: .envファイルのAPP_KEYを確認

```bash
cat .env | grep APP_KEY
```

`APP_KEY`が設定されているか確認してください。

### ステップ6: 一時的にデバッグモードを有効化

```bash
vi .env
```

以下を変更：

```env
APP_DEBUG=true
```

### ステップ7: キャッシュをクリア

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### ステップ8: 動作確認

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/
```

`APP_DEBUG=true`に設定している場合、詳細なエラーメッセージが表示されます。

---

## 🔍 追加の確認事項

### 1. ファイルの存在確認

```bash
ls -la index.php
ls -la .htaccess
ls -la vendor/autoload.php
ls -la bootstrap/app.php
```

すべてのファイルが存在するか確認してください。

### 2. 権限の確認

```bash
ls -la index.php .htaccess
```

権限が正しいか確認：
- `index.php` → **644**
- `.htaccess` → **644**

### 3. publicディレクトリの確認

```bash
ls -la public/
```

`public`ディレクトリが空か、または存在しないことを確認してください。

---

## ✅ チェックリスト

- [ ] `.htaccess`が`baseball_slg`と完全に同じ内容になっている
- [ ] `.htaccess`の権限が **644** に設定されている
- [ ] `index.php`のパスが正しく修正されている（`../`が含まれていない）
- [ ] `public/index.php`が存在しない（削除済み）
- [ ] `APP_KEY`が設定されている
- [ ] 一時的に`APP_DEBUG=true`に設定した
- [ ] キャッシュをクリアした
- [ ] `/cafejob/`にアクセスして動作確認した

---

## 🎯 推奨される手順

1. **`.htaccess`を`baseball_slg`と完全に同じ内容に再作成**
2. **`index.php`のパスを再確認**
3. **`.env`ファイルの`APP_KEY`を確認**
4. **一時的に`APP_DEBUG=true`に設定**
5. **キャッシュをクリア**
6. **ブラウザでアクセスして詳細なエラーメッセージを確認**

