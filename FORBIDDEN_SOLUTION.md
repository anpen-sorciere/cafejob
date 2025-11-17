# Forbidden エラーの解決方法（プロジェクトルート構成の場合）

## 📍 現在の状況

- `/cafejob/` はプロジェクトルート（権限: 775）
- `/cafejob/public/` が存在する
- `/cafejob/public/index.php` が存在する
- さくらサーバーで `/cafejob/` がプロジェクトルートを指している

## 🔍 問題の原因

さくらサーバーで `/cafejob/` がプロジェクトルートを指している場合、Webサーバーは `/cafejob/` に直接アクセスしようとします。しかし、Laravelのエントリーポイントは `public/index.php` です。

そのため、以下のいずれかが必要です：

1. **さくらサーバーの設定で `/cafejob/` を `public` ディレクトリに変更する**（推奨）
2. **プロジェクトルートの `.htaccess` で `public` にリダイレクトする**（既に試したが失敗）
3. **`public` ディレクトリとファイルの権限を確認する**

---

## 🔧 解決方法

### 方法1: さくらサーバーの設定を変更（最も確実）

さくらサーバーのコントロールパネルで：

1. **「サーバーの設定」→「サブディレクトリ」を選択**
2. **`/cafejob/` の設定を確認**
3. **設定を変更して、`public` ディレクトリを指すようにする**

**設定方法:**
- サブディレクトリのパスを `/cafejob/public` に変更
- または、ドキュメントルートを `public` ディレクトリに設定

これにより、`https://purplelion51.sakura.ne.jp/cafejob/` にアクセスすると、自動的に `public` ディレクトリの内容が表示されます。

### 方法2: ディレクトリとファイルの権限を確認・設定

FTPクライアントで以下の権限を設定してください：

**設定する権限:**

1. **`/cafejob/public/` → 755**
   - FileZilla: ディレクトリを右クリック → 「ファイルの属性」→ `755` を入力

2. **`/cafejob/public/index.php` → 644**
   - FileZilla: ファイルを右クリック → 「ファイルの属性」→ `644` を入力

3. **`/cafejob/public/.htaccess` → 644**
   - FileZilla: ファイルを右クリック → 「ファイルの属性」→ `644` を入力

4. **`/cafejob/` → 755（775のままでもOK）**
   - プロジェクトルートの権限は775のままでも問題ありません

### 方法3: プロジェクトルートの `.htaccess` を再確認

プロジェクトルート（`/cafejob/.htaccess`）に以下の内容を設定：

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

**注意**: この方法が動作しない場合、さくらサーバーの設定で `.htaccess` の使用が制限されている可能性があります。

### 方法4: `public/index.php` の内容を確認

FTPで `/cafejob/public/index.php` をダウンロードして、以下の内容が含まれているか確認してください：

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ... 以下省略
```

もし内容が正しくない場合、ローカルの `public/index.php` を再アップロードしてください。

---

## 🎯 推奨される手順

### ステップ1: 権限を設定

1. **`/cafejob/public/` → 755**
2. **`/cafejob/public/index.php` → 644**
3. **`/cafejob/public/.htaccess` → 644**

### ステップ2: さくらサーバーの設定を確認

さくらサーバーのコントロールパネルで：

1. **サブディレクトリの設定を確認**
   - `/cafejob/` がどのディレクトリを指しているか確認

2. **可能であれば、`public` ディレクトリを指すように変更**
   - これが最も確実な方法です

### ステップ3: エラーログを確認

さくらサーバーのコントロールパネルで：

1. **「ログ」→「エラーログ」を選択**
2. **最新のエラーメッセージを確認**
3. **エラーの詳細を確認**

---

## 🔍 追加の確認事項

### 1. `public/index.php` の権限

`/cafejob/public/index.php` の権限が **644** になっているか確認してください。

### 2. `public/.htaccess` の存在

`/cafejob/public/.htaccess` が存在し、権限が **644** になっているか確認してください。

### 3. ディレクトリの構造

以下の構造になっているか確認：

```
/cafejob/              (プロジェクトルート、権限: 775)
├── .htaccess          (プロジェクトルートの .htaccess)
├── public/            (権限: 755)
│   ├── index.php     (権限: 644)
│   ├── .htaccess     (権限: 644)
│   └── assets/
├── app/
├── config/
└── .env
```

---

## ✅ チェックリスト

以下を順番に確認・実行してください：

- [ ] `/cafejob/public/` の権限が **755** に設定されている
- [ ] `/cafejob/public/index.php` の権限が **644** に設定されている
- [ ] `/cafejob/public/.htaccess` の権限が **644** に設定されている
- [ ] `/cafejob/public/index.php` の内容が正しい（ローカルの `public/index.php` と一致）
- [ ] さくらサーバーの設定で `/cafejob/` が `public` ディレクトリを指すように変更（可能であれば）
- [ ] エラーログを確認して、詳細なエラーメッセージを確認

---

## 📞 さくらサーバーのサポートに問い合わせる場合

以下の情報を準備して問い合わせてください：

1. **エラーメッセージ**: "Forbidden - You don't have permission to access this resource"
2. **アクセスURL**: `https://purplelion51.sakura.ne.jp/cafejob/`
3. **ディレクトリ構造**: `/cafejob/` がプロジェクトルートで、`public` ディレクトリがその中にある
4. **権限設定**: `/cafejob/` は775、`/cafejob/public/` は755（設定予定）
5. **質問**: サブディレクトリ `/cafejob/` を `public` ディレクトリに設定する方法

---

## 🎯 次のアクション

1. **まず、`public` ディレクトリとファイルの権限を設定**
   - `/cafejob/public/` → 755
   - `/cafejob/public/index.php` → 644
   - `/cafejob/public/.htaccess` → 644

2. **さくらサーバーのコントロールパネルで設定を確認**
   - 可能であれば、`/cafejob/` を `public` ディレクトリに設定

3. **エラーログを確認**
   - 詳細なエラーメッセージを確認

4. **結果を確認**
   - ブラウザで `https://purplelion51.sakura.ne.jp/cafejob/` にアクセス

