# Forbidden エラーの解決方法

## 🔍 エラーの原因

"Forbidden" エラーは、Webサーバーがアクセスを拒否している状態です。さくらサーバーでサブディレクトリにLaravelを配置する場合、以下の原因が考えられます：

1. **プロジェクトルートの `.htaccess` が原因**
   - さくらサーバーで `/cafejob/` が既に `public` ディレクトリを指している場合、プロジェクトルートの `.htaccess` が不要で、むしろ問題を引き起こす可能性があります

2. **ディレクトリの権限設定**
   - `public` ディレクトリやその中のファイルの権限が不適切

3. **さくらサーバーの設定**
   - サブディレクトリの設定が `public` ディレクトリを指していない

---

## 🔧 解決方法

### 方法1: プロジェクトルートの `.htaccess` を削除または修正

さくらサーバーで `/cafejob/` が既に `public` ディレクトリを指している場合：

1. **FTPでサーバーに接続**
2. **プロジェクトルート（`/cafejob/`）の `.htaccess` を削除またはリネーム**
   - 削除するか、`.htaccess.backup` にリネーム
3. **ブラウザで再アクセス**
   - `https://purplelion51.sakura.ne.jp/cafejob/` にアクセス

### 方法2: `.htaccess` の設定を修正

プロジェクトルートの `.htaccess` を以下の内容に変更：

```apache
# Laravel サブディレクトリ配置用 .htaccess（修正版）

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # 既に /cafejob/public/ にいる場合は何もしない
    RewriteCond %{REQUEST_URI} ^/cafejob/public/
    RewriteRule ^ - [L]
    
    # ファイルやディレクトリが存在する場合は何もしない
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # /cafejob/public/ へのリダイレクト
    RewriteRule ^(.*)$ /cafejob/public/$1 [L]
</IfModule>

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### 方法3: さくらサーバーの設定を確認

さくらサーバーのコントロールパネルで：

1. **サブディレクトリの設定を確認**
   - `/cafejob/` がどのディレクトリを指しているか確認
   - プロジェクトルートを指している場合 → 方法1または方法2を試す
   - `public` ディレクトリを指している場合 → プロジェクトルートの `.htaccess` を削除

---

## 📋 確認手順

### ステップ1: 現在の設定を確認

FTPでサーバーに接続し、以下を確認：

1. **プロジェクトルート（`/cafejob/`）の構造**
   ```
   /cafejob/
   ├── .htaccess          ← これが存在するか確認
   ├── app/
   ├── public/
   │   ├── index.php
   │   └── .htaccess
   └── .env
   ```

2. **`public` ディレクトリの内容**
   - `public/index.php` が存在するか確認
   - `public/.htaccess` が存在するか確認

### ステップ2: テスト

1. **プロジェクトルートの `.htaccess` を一時的に削除**
   - `.htaccess.backup` にリネーム
2. **ブラウザでアクセス**
   - `https://purplelion51.sakura.ne.jp/cafejob/`
3. **結果を確認**
   - 正常に表示される → プロジェクトルートの `.htaccess` は不要
   - まだエラー → 他の原因を確認

### ステップ3: ディレクトリの権限を確認

FTPクライアントで以下の権限を確認：

- `public` → **755** または **775**
- `public/index.php` → **644** または **664**
- `public/.htaccess` → **644** または **664**

---

## 🎯 推奨される対処法

### パターンA: `/cafejob/` がプロジェクトルートを指している場合

1. **プロジェクトルートの `.htaccess` を修正**（方法2を参照）
2. または、さくらサーバーの設定で `/cafejob/` を `public` ディレクトリに変更

### パターンB: `/cafejob/` が既に `public` ディレクトリを指している場合

1. **プロジェクトルートの `.htaccess` を削除**
2. **FTPでアップロードする際は、`public` ディレクトリの内容のみをアップロード**
   - **注意**: この方法はセキュリティ上の理由から推奨しません

---

## 🔍 追加の確認事項

### 1. `public/index.php` が存在するか確認

FTPで `public/index.php` が存在することを確認してください。

### 2. ディレクトリの権限を確認

以下の権限が設定されているか確認：

- `public` → **755**
- `public/index.php` → **644**
- `public/.htaccess` → **644**

### 3. エラーログを確認

さくらサーバーのコントロールパネルで：

1. 「ログ」→「エラーログ」を選択
2. 最新のエラーメッセージを確認
3. エラーの詳細を確認

---

## ✅ 解決後の確認

正常に動作するようになったら：

1. **ブラウザでアクセス**
   - `https://purplelion51.sakura.ne.jp/cafejob/`
2. **Laravelのページが表示されることを確認**
3. **エラーが表示されないことを確認**

---

## 📞 それでも解決しない場合

1. **さくらサーバーのサポートに問い合わせ**
   - サブディレクトリの設定方法を確認
   - PHPのバージョンや拡張機能を確認

2. **エラーログを確認**
   - サーバーのエラーログを確認
   - 詳細なエラーメッセージを確認

3. **設定を再確認**
   - `.env` ファイルの設定
   - ファイルの権限設定
   - ディレクトリ構造

