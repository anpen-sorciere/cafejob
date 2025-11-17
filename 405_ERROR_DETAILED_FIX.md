# 405エラーの詳細な解決方法

## 🔍 現在の状況

- `/cafejob/` → **405 Method Not Allowed** エラー ❌
- `public/index.php`は削除済み
- Laravelは起動しようとしている

## 📋 405エラーの原因

405エラーは、HTTPメソッドが許可されていないことを示しています。これは通常、以下の原因が考えられます：

1. **`.htaccess`の設定が正しくない**
   - `baseball_slg`と同じ`.htaccess`になっていない可能性

2. **ルーティングの問題**
   - Laravelのルーティングが正しく動作していない

3. **HTTPメソッドの問題**
   - GETリクエストが正しく処理されていない

---

## 🔧 解決方法

### ステップ1: .htaccessの内容を確認

SSHで以下を実行：

```bash
cd /home/purplelion51/www/cafejob
cat .htaccess
```

`baseball_slg`と同じ内容になっているか確認してください。

**正しい内容:**

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
```

### ステップ2: baseball_slgの.htaccessと比較

```bash
cat /home/purplelion51/www/baseball_slg/.htaccess
```

内容が完全に同じか確認してください。

### ステップ3: index.phpのパスを再確認

```bash
cat index.php | grep -E "(vendor|bootstrap)"
```

以下のように表示されるはずです：

```
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

### ステップ4: 一時的にデバッグモードを有効化

`.env`ファイルで：

```bash
vi .env
```

以下を変更：

```env
APP_DEBUG=true
```

これで詳細なエラーメッセージが表示されます。

### ステップ5: ログファイルを確認

```bash
tail -100 storage/logs/laravel.log
```

最新のエラーメッセージを確認してください。

### ステップ6: .htaccessを再作成

`baseball_slg`と完全に同じ内容で再作成：

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
EOF

chmod 644 .htaccess
```

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

### 3. baseball_slgとの比較

```bash
diff .htaccess /home/purplelion51/www/baseball_slg/.htaccess
```

違いがあれば、`baseball_slg`と同じ内容に修正してください。

---

## ✅ チェックリスト

- [ ] `.htaccess`が`baseball_slg`と完全に同じ内容になっている
- [ ] `index.php`のパスが正しく修正されている（`../`が含まれていない）
- [ ] `public/index.php`が存在しない（削除済み）
- [ ] `APP_KEY`が設定されている
- [ ] 一時的に`APP_DEBUG=true`に設定して詳細なエラーを確認した
- [ ] ログファイルを確認した

---

## 🎯 推奨される手順

1. **`.htaccess`を`baseball_slg`と完全に同じ内容に再作成**
2. **`index.php`のパスを再確認**
3. **一時的に`APP_DEBUG=true`に設定**
4. **ログファイルを確認**
5. **ブラウザでアクセスして詳細なエラーメッセージを確認**

