# .htaccess 403エラーの診断と解決方法

## 🔍 現在の状況

- `/cafejob/.htaccess` ファイルが存在する ✅
- 権限が **644** に設定されている ✅
- 内容が正しい ✅
- しかし、`/cafejob/` にアクセスすると **403エラー** ❌

## 📋 考えられる原因

1. **さくらサーバーの設定で `.htaccess` が無効になっている**
2. **サブディレクトリの設定で `.htaccess` のリライトルールが動作しない**
3. **ディレクトリの権限が不適切**
4. **`.htaccess` の構文エラー（さくらサーバーで認識されない）**

---

## 🔧 解決方法

### 方法1: エラーログを確認

さくらサーバーのコントロールパネルで：

1. **「ログ」→「エラーログ」を選択**
2. **最新のエラーメッセージを確認**
3. **403エラーの詳細な原因を確認**

エラーログに以下のようなメッセージが表示される可能性があります：
- `.htaccess` に関するエラー
- 権限に関するエラー
- リライトルールに関するエラー

### 方法2: `.htaccess` の内容を変更（さくらサーバー用）

さくらサーバーのスタンダードプランでは、`.htaccess` のリライトルールが正しく動作しない場合があります。

以下の内容に変更してみてください：

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /cafejob/
    
    # publicディレクトリへのリダイレクト
    RewriteCond %{REQUEST_URI} !^/cafejob/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# .envファイルの保護
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

**変更点:**
- `RewriteBase /cafejob/` を追加
- `RewriteRule` を `public/$1` に変更（相対パス）

### 方法3: ディレクトリの権限を確認

SSHで以下を実行：

```bash
cd /cafejob
ls -la
```

以下の権限を確認：
- `/cafejob/` → **755**
- `/cafejob/.htaccess` → **644**
- `/cafejob/public/` → **755**

### 方法4: テストファイルで確認

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

## 🎯 推奨される手順

### ステップ1: エラーログを確認

さくらサーバーのコントロールパネルでエラーログを確認してください。

### ステップ2: `.htaccess` の内容を変更

上記の方法2で `.htaccess` の内容を変更してみてください。

### ステップ3: 権限を再確認

```bash
chmod 755 /cafejob
chmod 644 /cafejob/.htaccess
chmod 755 /cafejob/public
```

### ステップ4: 動作確認

ブラウザで `/cafejob/` にアクセスして確認してください。

---

## 🔍 追加の確認事項

### 1. さくらサーバーの設定を確認

さくらサーバーのコントロールパネルで：

1. **「サーバーの設定」→「Apache設定」を確認**
   - `.htaccess` の使用が有効になっているか確認

2. **「サーバーの設定」→「サブディレクトリ」を確認**
   - `/cafejob/` の設定を確認

### 2. 別のアプローチ: シンボリックリンクを使用

さくらサーバーで `.htaccess` が動作しない場合、別の方法として：

1. **`/cafejob/` を `public` ディレクトリにシンボリックリンク**
   - ただし、これはセキュリティ上の理由から推奨しません

2. **さくらサーバーのコントロールパネルで設定を変更**
   - `/cafejob/` を `public` ディレクトリに設定

---

## ✅ チェックリスト

- [ ] エラーログを確認した
- [ ] `.htaccess` の内容を変更した（`RewriteBase` を追加）
- [ ] ディレクトリの権限を確認した
- [ ] テストファイルで動作確認した
- [ ] さくらサーバーの設定を確認した

---

## 📞 さくらサーバーのサポートに問い合わせる場合

以下の情報を準備して問い合わせてください：

1. **プラン**: スタンダードプラン
2. **エラーメッセージ**: "403 Forbidden"
3. **アクセスURL**: `https://purplelion51.sakura.ne.jp/cafejob/`
4. **状況**: `.htaccess` ファイルは存在し、権限も644、内容も正しいが、403エラーが発生
5. **質問**: サブディレクトリで `.htaccess` のリライトルールが動作しない原因

