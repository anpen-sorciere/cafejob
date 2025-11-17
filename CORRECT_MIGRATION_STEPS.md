# 正しい移行手順（パス修正版）

## 🔍 正しいパス

さくらサーバーでは、プロジェクトのパスは：
```
/home/purplelion51/www/cafejob
```

`cd /cafejob` ではなく、`cd ~/www/cafejob` または `cd /home/purplelion51/www/cafejob` を使用してください。

---

## 🔧 正しい移行手順

### ステップ1: プロジェクトディレクトリに移動

```bash
cd /home/purplelion51/www/cafejob
```

または：

```bash
cd ~/www/cafejob
```

### ステップ2: publicディレクトリの内容をプロジェクトルートに移動

```bash
mv public/index.php .
mv public/.htaccess .htaccess.public.backup
mv public/assets .
mv public/build .
mv public/favicon.ico .
mv public/robots.txt .
mv public/storage .
```

### ステップ3: index.phpのパスを修正

sedコマンドが動作しない場合、直接編集する方法：

```bash
cd /home/purplelion51/www/cafejob
```

**方法A: sedコマンドを使用（Linux形式）**

```bash
sed -i 's|__DIR__\.'\''/\.\./vendor|__DIR__.'\''/vendor|g' index.php
sed -i 's|__DIR__\.'\''/\.\./bootstrap|__DIR__.'\''/bootstrap|g' index.php
sed -i 's|__DIR__\.'\''/\.\./storage|__DIR__.'\''/storage|g' index.php
```

**方法B: 直接編集（推奨）**

```bash
vi index.php
```

または：

```bash
nano index.php
```

以下の3行を修正：

**変更前:**
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
```

**変更後:**
```php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
```

### ステップ4: .htaccessを更新

```bash
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

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
EOF

chmod 644 .htaccess
```

### ステップ5: 権限を設定

```bash
chmod 644 index.php
chmod -R 755 assets
chmod -R 755 build
```

### ステップ6: 動作確認

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/
```

---

## 🔍 index.phpの修正内容（手動編集の場合）

`index.php`を開いて、以下の3箇所を修正：

1. **19行目付近:**
   ```php
   // 変更前
   if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
   
   // 変更後
   if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
   ```

2. **34行目付近:**
   ```php
   // 変更前
   require __DIR__.'/../vendor/autoload.php';
   
   // 変更後
   require __DIR__.'/vendor/autoload.php';
   ```

3. **47行目付近:**
   ```php
   // 変更前
   $app = require_once __DIR__.'/../bootstrap/app.php';
   
   // 変更後
   $app = require_once __DIR__.'/bootstrap/app.php';
   ```

---

## ✅ チェックリスト

- [ ] 正しいパス `/home/purplelion51/www/cafejob` に移動した
- [ ] `public`ディレクトリの内容をプロジェクトルートに移動した
- [ ] `index.php`のパスを修正した（3箇所）
- [ ] `.htaccess`を`baseball_slg`と同じ内容に変更した
- [ ] 権限を設定した
- [ ] `/cafejob/`にアクセスして動作確認した

