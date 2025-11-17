# さくらサーバーでの設定方法（他のプロジェクトへの影響なし）

## 📍 重要なポイント

**他のプロジェクトへの影響はありません。**

さくらサーバーでは、各サブディレクトリ（`/cafejob/`、`/other-project/` など）は独立して設定できます。`/cafejob/` の設定を変更しても、他のプロジェクトには影響しません。

---

## 🎯 解決方法（推奨）

### 方法1: `.htaccess` を使用する方法（他のプロジェクトに影響なし）

さくらサーバーのサブディレクトリ設定を変更する必要はありません。プロジェクトルートの `.htaccess` で `public` ディレクトリにリダイレクトします。

**現在の構造:**
```
/cafejob/              ← プロジェクトルート（さくらサーバーの設定は変更不要）
├── .htaccess          ← ここで public にリダイレクト
├── public/
│   ├── index.php
│   └── .htaccess
├── app/
└── config/
```

**他のプロジェクトへの影響:**
- `/other-project/` などの他のプロジェクトには一切影響しません
- 各サブディレクトリは独立して動作します

---

## 🔧 具体的な手順

### ステップ1: プロジェクトルートの `.htaccess` を確認

FTPで `/cafejob/.htaccess` を確認してください。

**正しい内容:**
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

### ステップ2: 権限を設定

FTPクライアントで以下の権限を設定：

1. **`/cafejob/public/` → 755**
2. **`/cafejob/public/index.php` → 644**
3. **`/cafejob/public/.htaccess` → 644**

### ステップ3: 動作確認

ブラウザで `https://purplelion51.sakura.ne.jp/cafejob/` にアクセス

---

## ❓ よくある質問

### Q1: さくらサーバーのサブディレクトリ設定を変更する必要はありますか？

**A:** いいえ、変更する必要はありません。

- さくらサーバーの設定はそのままでOKです
- プロジェクトルートの `.htaccess` で `public` ディレクトリにリダイレクトします
- これにより、他のプロジェクトへの影響はありません

### Q2: 他のプロジェクト（`/other-project/` など）に影響しますか？

**A:** いいえ、影響しません。

- 各サブディレクトリは独立して動作します
- `/cafejob/` の設定を変更しても、`/other-project/` には影響しません
- 各プロジェクトは独自の `.htaccess` を持っています

### Q3: なぜ `.htaccess` が動作しないのですか？

**A:** 以下の原因が考えられます：

1. **権限の問題**
   - `.htaccess` ファイルの権限が正しくない
   - ディレクトリの権限が正しくない

2. **mod_rewrite が有効でない**
   - さくらサーバーでは通常有効ですが、確認が必要

3. **`.htaccess` の構文エラー**
   - ファイルの内容に問題がある

---

## 🔍 トラブルシューティング

### `.htaccess` が動作しない場合

1. **さくらサーバーのコントロールパネルで確認**
   - 「サーバーの設定」→「Apache設定」を確認
   - `.htaccess` の使用が有効になっているか確認

2. **エラーログを確認**
   - さくらサーバーのコントロールパネルでエラーログを確認
   - `.htaccess` に関するエラーがないか確認

3. **別の方法を試す**
   - `public` ディレクトリの内容を直接 `/cafejob/` にコピー（非推奨）
   - または、さくらサーバーのサポートに問い合わせ

---

## ✅ 推奨される手順

1. **プロジェクトルートの `.htaccess` を確認**
   - 正しい内容になっているか確認

2. **権限を設定**
   - `/cafejob/public/` → 755
   - `/cafejob/public/index.php` → 644
   - `/cafejob/public/.htaccess` → 644

3. **動作確認**
   - ブラウザでアクセスして確認

4. **エラーが続く場合**
   - エラーログを確認
   - さくらサーバーのサポートに問い合わせ

---

## 📞 さくらサーバーのサポートに問い合わせる場合

以下の情報を準備して問い合わせてください：

1. **エラーメッセージ**: "Forbidden - You don't have permission to access this resource"
2. **アクセスURL**: `https://purplelion51.sakura.ne.jp/cafejob/`
3. **ディレクトリ構造**: `/cafejob/` がプロジェクトルートで、`public` ディレクトリがその中にある
4. **質問**: プロジェクトルートの `.htaccess` で `public` ディレクトリにリダイレクトする方法
5. **他のプロジェクトへの影響**: 他のプロジェクトに影響しない方法を希望

