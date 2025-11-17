# publicディレクトリをプロジェクトルートに移動する手順

## 🔍 現在の状況

`baseball_slg`プロジェクトと同じ方法を取る場合：

- `/baseball_slg/` = プロジェクトルート = `public`ディレクトリの内容が直接配置されている
- `/baseball_slg/public/` = 空（または存在しない）

cafejobプロジェクトも同じ構造にする必要があります。

---

## 🔧 移行手順

### ステップ1: publicディレクトリの内容を確認

現在の`public`ディレクトリの内容：
- `index.php`
- `.htaccess`
- `assets/`
- `build/`
- `favicon.ico`
- `robots.txt`
- `storage`（シンボリックリンク）

### ステップ2: publicディレクトリの内容をプロジェクトルートに移動

**SSHで実行：**

```bash
cd /cafejob
mv public/index.php .
mv public/.htaccess .htaccess.public
mv public/assets .
mv public/build .
mv public/favicon.ico .
mv public/robots.txt .
mv public/storage .
```

**注意**: `.htaccess`は既にプロジェクトルートにあるので、`public/.htaccess`を`.htaccess.public`にリネームしてバックアップとして保存します。

### ステップ3: index.phpのパスを修正

プロジェクトルートの`index.php`のパスを修正する必要があります。

**現在の`index.php`（publicディレクトリ内）:**
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**修正後（プロジェクトルート）:**
```php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

**SSHで実行：**

```bash
cd /cafejob
sed -i "s|__DIR__.'/../vendor|__DIR__.'/vendor|g" index.php
sed -i "s|__DIR__.'/../bootstrap|__DIR__.'/bootstrap|g" index.php
```

### ステップ4: .htaccessを更新

プロジェクトルートの`.htaccess`を、`baseball_slg`と同じ内容に変更：

```apache
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
```

**SSHで実行：**

```bash
cd /cafejob
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

### ステップ5: publicディレクトリを削除または空にする

`public`ディレクトリは空になるので、削除するか、そのままにしておきます。

```bash
cd /cafejob
rmdir public  # 空の場合のみ削除可能
```

または、空のままにしておきます。

### ステップ6: 権限を設定

```bash
chmod 644 index.php
chmod 644 .htaccess
chmod -R 755 assets
chmod -R 755 build
```

### ステップ7: 動作確認

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/
```

正常に表示されるか確認してください。

---

## ⚠️ 注意事項

1. **バックアップを取る**
   - 移行前に、`public`ディレクトリのバックアップを取ってください

2. **index.phpのパス修正**
   - `index.php`のパスを必ず修正してください
   - 修正しないと、Laravelが起動しません

3. **.htaccessの更新**
   - `baseball_slg`と同じ`.htaccess`に変更してください

4. **storageシンボリックリンク**
   - `storage`シンボリックリンクも移動する必要があります
   - または、新しく作成する必要があります

---

## ✅ チェックリスト

- [ ] `public`ディレクトリの内容をプロジェクトルートに移動した
- [ ] `index.php`のパスを修正した
- [ ] `.htaccess`を`baseball_slg`と同じ内容に変更した
- [ ] 権限を設定した
- [ ] `/cafejob/`にアクセスして動作確認した

---

## 🎯 期待される結果

移行後、以下の構造になります：

```
/cafejob/              ← プロジェクトルート
├── index.php          ← public/index.phpから移動
├── .htaccess          ← baseball_slgと同じ内容
├── assets/            ← public/assetsから移動
├── build/             ← public/buildから移動
├── favicon.ico        ← public/favicon.icoから移動
├── robots.txt         ← public/robots.txtから移動
├── storage            ← public/storageから移動（シンボリックリンク）
├── app/
├── bootstrap/
├── config/
└── vendor/
```

これで、`baseball_slg`と同じ構造になり、正常に動作するはずです。

