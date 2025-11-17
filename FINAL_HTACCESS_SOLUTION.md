# 最終的な .htaccess の解決方法

## 🔍 現在の状況

- `/cafejob/public/index.php` → **正常に表示される** ✅
- `/cafejob/` → **403エラー** ❌

## 📋 問題の原因

さくらサーバーのスタンダードプランでは、サブディレクトリでの `.htaccess` の動作が制限されている可能性があります。

**考えられる原因:**
1. `RewriteBase` が正しく動作していない
2. リライトルールが複雑すぎる
3. さくらサーバーの設定で `.htaccess` の動作が制限されている

---

## 🔧 解決方法

### 最もシンプルな `.htaccess` の内容

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

**変更点:**
1. `RewriteBase` を削除（絶対パスを使用）
2. リライトルールを最もシンプルな形に変更
3. 絶対パス `/cafejob/public/$1` を使用

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

### ステップ2: 動作確認

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/
```

正常に表示されるか確認してください。

---

## 🔍 それでも403エラーが続く場合

### 方法1: さくらサーバーの設定を確認

さくらサーバーのコントロールパネルで：

1. **「サーバーの設定」→「Apache設定」を確認**
   - `.htaccess` の使用が有効になっているか確認

2. **「サーバーの設定」→「サブディレクトリ」を確認**
   - `/cafejob/` の設定を確認
   - 可能であれば、`public` ディレクトリを直接指すように設定

### 方法2: エラーログを確認

さくらサーバーのコントロールパネルで：

1. **「ログ」→「エラーログ」を選択**
2. **最新のエラーメッセージを確認**
3. **403エラーの詳細な原因を確認**

### 方法3: 一時的な回避策

`.htaccess` が動作しない場合、以下の方法を検討：

1. **`/cafejob/` を `public` ディレクトリにシンボリックリンク**
   - ただし、これはセキュリティ上の理由から推奨しません

2. **さくらサーバーのコントロールパネルで設定を変更**
   - `/cafejob/` を `public` ディレクトリに設定
   - 他のプロジェクトへの影響を確認

---

## ✅ チェックリスト

- [ ] `.htaccess` の内容を最もシンプルな形に変更した
- [ ] `RewriteBase` を削除して絶対パスを使用した
- [ ] `.htaccess` の権限が **644** に設定されている
- [ ] `/cafejob/` にアクセスして動作確認した
- [ ] エラーログを確認した

---

## 🎯 期待される動作

修正後、以下のように動作するはずです：

1. `/cafejob/` にアクセス
   → `/cafejob/public/` にリダイレクト
   → `public/index.php` が処理される
   → Laravelアプリケーションが表示される

2. `/cafejob/jobs` にアクセス
   → `/cafejob/public/jobs` にリダイレクト
   → Laravelのルーティングが処理される

---

## 📞 さくらサーバーのサポートに問い合わせる場合

以下の情報を準備して問い合わせてください：

1. **プラン**: スタンダードプラン
2. **エラーメッセージ**: "403 Forbidden"
3. **アクセスURL**: `https://purplelion51.sakura.ne.jp/cafejob/`
4. **状況**: `.htaccess` ファイルは存在し、権限も644、内容も正しいが、403エラーが発生
5. **質問**: サブディレクトリで `.htaccess` のリライトルールが動作しない原因

