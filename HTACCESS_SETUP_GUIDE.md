# .htaccess ファイルの設置手順（詳細ガイド）

## 📍 「プロジェクトルート」とは？

**プロジェクトルート**とは、Laravelプロジェクトの**一番上のディレクトリ**のことです。

### 現在のプロジェクト構造

```
c:\xampp\htdocs\cafejob\          ← これが「プロジェクトルート」
├── app\                           ← アプリケーションコード
├── public\                        ← 公開ディレクトリ
│   ├── index.php
│   └── .htaccess                  ← 既に存在（これは別物）
├── config\
├── routes\
├── storage\
├── .htaccess                      ← ここに作成するファイル（新規）
├── .env
├── artisan
└── composer.json
```

**重要なポイント：**
- プロジェクトルート = `c:\xampp\htdocs\cafejob\`（ローカル環境）
- サーバー上では、FTPでアップロードした**一番上のディレクトリ**がプロジェクトルートです

---

## 📄 .htaccess ファイルとは？

`.htaccess` は、Apache（Webサーバー）の設定ファイルです。このファイルを配置することで、以下のことができます：

1. **URLのリダイレクト**：`/cafejob/` にアクセスしたら、自動的に `/cafejob/public/` に転送
2. **セキュリティ保護**：`.env` ファイルなど、重要なファイルへのアクセスをブロック

---

## 🎯 手順1: ローカル環境でファイルを確認

既に `.htaccess` ファイルは作成済みです。以下の場所にあります：

**ファイルの場所：**
```
c:\xampp\htdocs\cafejob\.htaccess
```

**ファイルの内容：**
```apache
# Laravel サブディレクトリ配置用 .htaccess
# プロジェクトルートに配置してください

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # /cafejob/public/ へのリダイレクト
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

---

## 🚀 手順2: FTPでサーバーにアップロード

### 方法A: FileZillaを使用する場合

1. **FileZillaを起動**
   - FTPサーバーに接続

2. **ローカル側（左側）でファイルを探す**
   - `c:\xampp\htdocs\cafejob\` に移動
   - `.htaccess` ファイルを探す
   - **注意**: `.htaccess` は隠しファイルなので、表示されない場合があります
     - FileZillaのメニュー: 「サーバー」→「強制表示」を選択
     - または、ファイル名で直接検索

3. **サーバー側（右側）でアップロード先を確認**
   - `/cafejob/` ディレクトリに移動（プロジェクトルート）
   - 以下のような構造になっていることを確認：
     ```
     /cafejob/
     ├── app/
     ├── public/
     ├── config/
     ├── .env
     └── （ここに .htaccess をアップロード）
     ```

4. **ファイルをアップロード**
   - ローカル側の `.htaccess` を**右クリック**
   - 「アップロード」を選択
   - サーバー側の `/cafejob/` ディレクトリにアップロードされる

### 方法B: WinSCPを使用する場合

1. **WinSCPを起動**
   - FTPサーバーに接続

2. **ローカル側（左側）でファイルを探す**
   - `c:\xampp\htdocs\cafejob\` に移動
   - `.htaccess` ファイルを探す
   - **注意**: 隠しファイルを表示する設定を有効にする
     - メニュー: 「オプション」→「設定」→「パネル」→「隠しファイルを表示」

3. **サーバー側（右側）でアップロード先を確認**
   - `/cafejob/` ディレクトリに移動

4. **ファイルをアップロード**
   - ローカル側の `.htaccess` を**ドラッグ&ドロップ**でサーバー側にコピー

### 方法C: サーバー上で直接作成する場合

FTPクライアントでサーバーに接続し、以下の手順で作成：

1. **サーバー側の `/cafejob/` ディレクトリに移動**

2. **新しいファイルを作成**
   - FileZillaの場合: 右クリック → 「ファイルを作成」
   - WinSCPの場合: 右クリック → 「新規」→「ファイル」

3. **ファイル名を入力**
   - ファイル名: `.htaccess`（先頭のドットを含む）

4. **ファイルを編集**
   - ファイルを右クリック → 「編集」を選択
   - 以下の内容を貼り付け：

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # /cafejob/public/ へのリダイレクト
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

5. **保存**
   - ファイルを保存して閉じる

---

## ✅ 手順3: アップロード後の確認

### サーバー上のファイル構造を確認

FTPクライアントで、サーバー側の `/cafejob/` ディレクトリを確認してください。

**正しい構造：**
```
/cafejob/
├── .htaccess          ← これがプロジェクトルートにあること
├── app/
├── public/
│   ├── .htaccess      ← これは別物（既に存在）
│   └── index.php
├── config/
├── .env
└── （その他のファイル）
```

**重要なポイント：**
- `.htaccess` は `/cafejob/` の**直下**（プロジェクトルート）に配置
- `public/.htaccess` とは**別のファイル**です

---

## 🔍 トラブルシューティング

### Q1: `.htaccess` ファイルが見つからない

**原因**: 隠しファイルが表示されていない

**解決方法：**
- FileZilla: 「サーバー」→「強制表示」を選択
- WinSCP: 「オプション」→「設定」→「パネル」→「隠しファイルを表示」にチェック
- Windowsエクスプローラー: 「表示」タブ → 「隠しファイル」にチェック

### Q2: ファイル名が `.htaccess.txt` になってしまう

**原因**: Windowsが拡張子を自動的に追加している

**解決方法：**
1. ファイル名を `.htaccess.`（最後にドット）に変更
2. または、FTPクライアントで直接 `.htaccess` として作成

### Q3: アップロードしたが動作しない

**確認事項：**
1. ファイルが正しい場所（`/cafejob/` の直下）にあるか確認
2. ファイルの内容が正しいか確認（改行コードなど）
3. サーバーで `.htaccess` が有効になっているか確認（さくらサーバーでは通常有効）

### Q4: `/cafejob/` が既に `public` ディレクトリを指している場合

**確認方法：**
- `https://purplelion51.sakura.ne.jp/cafejob/` にアクセス
- Laravelのページが表示される → `.htaccess` は不要（削除してください）
- エラーが表示される → `.htaccess` が必要

---

## 📝 まとめ

1. **プロジェクトルート** = Laravelプロジェクトの一番上のディレクトリ（`/cafejob/`）
2. **`.htaccess` ファイル** = プロジェクトルートに配置する設定ファイル
3. **アップロード方法** = FTPクライアントで `/cafejob/` ディレクトリにアップロード
4. **確認** = サーバー側で `/cafejob/.htaccess` が存在することを確認

これで、`https://purplelion51.sakura.ne.jp/cafejob/` にアクセスした際に、自動的に `public` ディレクトリの内容が表示されるようになります。

