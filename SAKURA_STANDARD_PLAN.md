# さくらインターネット スタンダードプランでのLaravel設定

## 📋 スタンダードプランの特徴

さくらインターネットのスタンダードプランでは、以下の特徴があります：

1. **`.htaccess` の使用が可能**
   - 基本的に `.htaccess` は使用できます
   - ただし、一部の機能が制限される場合があります

2. **サブディレクトリの設定**
   - サブディレクトリは独立して動作します
   - 各プロジェクトは独自の設定を持てます

3. **PHPのバージョン**
   - PHP 8.x が使用可能（Laravel 9 に対応）

---

## 🔧 スタンダードプランでの解決方法

### 方法1: `.htaccess` を使用する方法（推奨）

スタンダードプランでは、`.htaccess` を使用して `public` ディレクトリにリダイレクトできます。

**プロジェクトルートの `.htaccess` の内容:**
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

### 方法2: さくらサーバーのコントロールパネルで設定

スタンダードプランでは、コントロールパネルからサブディレクトリの設定を変更できる場合があります。

**設定手順:**
1. さくらサーバーのコントロールパネルにログイン
2. 「サーバーの設定」→「サブディレクトリ」を選択
3. `/cafejob/` の設定を確認
4. 必要に応じて設定を変更

**注意**: コントロールパネルで設定を変更する場合、他のプロジェクトへの影響を確認してください。

---

## 🔍 トラブルシューティング

### `.htaccess` が動作しない場合

スタンダードプランで `.htaccess` が動作しない場合、以下の原因が考えられます：

1. **権限の問題**
   - `.htaccess` ファイルの権限が正しくない
   - ディレクトリの権限が正しくない

2. **mod_rewrite が有効でない**
   - スタンダードプランでは通常有効ですが、確認が必要

3. **`.htaccess` の構文エラー**
   - ファイルの内容に問題がある

### 確認手順

1. **`.htaccess` ファイルの権限を確認**
   - `/cafejob/.htaccess` → 644

2. **ディレクトリの権限を確認**
   - `/cafejob/` → 755
   - `/cafejob/public/` → 755

3. **エラーログを確認**
   - さくらサーバーのコントロールパネルでエラーログを確認

---

## ✅ 推奨される手順

### ステップ1: 権限を設定

FTPクライアントで以下の権限を設定：

1. `/cafejob/.htaccess` → 644
2. `/cafejob/public/` → 755
3. `/cafejob/public/index.php` → 644
4. `/cafejob/public/.htaccess` → 644

### ステップ2: `.htaccess` の内容を確認

プロジェクトルートの `.htaccess` が正しい内容になっているか確認：

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

### ステップ3: 動作確認

ブラウザで `https://purplelion51.sakura.ne.jp/cafejob/` にアクセス

### ステップ4: エラーが続く場合

1. **エラーログを確認**
   - さくらサーバーのコントロールパネルでエラーログを確認

2. **さくらサーバーのサポートに問い合わせ**
   - スタンダードプランでの `.htaccess` の使用について確認

---

## 📞 さくらサーバーのサポートに問い合わせる場合

以下の情報を準備して問い合わせてください：

1. **プラン**: スタンダードプラン
2. **エラーメッセージ**: "Forbidden - You don't have permission to access this resource"
3. **アクセスURL**: `https://purplelion51.sakura.ne.jp/cafejob/`
4. **ディレクトリ構造**: `/cafejob/` がプロジェクトルートで、`public` ディレクトリがその中にある
5. **質問**: スタンダードプランで、プロジェクトルートの `.htaccess` で `public` ディレクトリにリダイレクトする方法

---

## 🎯 次のアクション

1. **権限を設定**
   - `/cafejob/.htaccess` → 644
   - `/cafejob/public/` → 755
   - `/cafejob/public/index.php` → 644

2. **`.htaccess` の内容を確認**
   - プロジェクトルートの `.htaccess` が正しい内容になっているか確認

3. **動作確認**
   - ブラウザでアクセスして確認

4. **エラーが続く場合**
   - エラーログを確認
   - さくらサーバーのサポートに問い合わせ

