# Laravel アプリケーション FTPサーバーへのデプロイ手順

## 📋 クイックスタート（最小限の手順）

FTPサーバーにアップロード済みの場合、以下の**最低限必要な手順**を実行してください：

1. **`.env` ファイルを作成・設定**
   - `.env.example` をコピーして `.env` を作成
   - データベース情報を設定
   - `APP_URL=https://purplelion51.sakura.ne.jp/cafejob` を設定（**末尾にスラッシュなし**）

2. **アプリケーションキーを生成**
   - ローカルで `php artisan key:generate` を実行
   - 生成された `APP_KEY` をサーバーの `.env` にコピー

3. **ディレクトリの権限を設定**
   - `storage` → **775**
   - `bootstrap/cache` → **775**

4. **サブディレクトリ用の設定**
   - プロジェクトルートに `.htaccess` ファイルを配置（既に作成済み）
   - `/cafejob/` がプロジェクトルートを指している場合、この `.htaccess` が `public` にリダイレクトします

5. **データベースマイグレーションを実行**
   - SSHで `php artisan migrate --force` を実行（SSHがない場合はサーバー管理者に依頼）

6. **動作確認**
   - `https://purplelion51.sakura.ne.jp/cafejob/` にアクセスして確認

---

## 前提条件

- FTPサーバーにファイルをアップロード済み
- サーバーでPHP 8.0.2以上が利用可能
- サーバーでComposerが利用可能（またはvendorディレクトリをアップロード済み）

## デプロイ手順

### 1. ファイル構造の確認

FTPサーバー上で以下の構造になっていることを確認してください：

```
サーバーのルートディレクトリ/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← これがWebサーバーのドキュメントルートになる必要があります
│   ├── index.php
│   └── assets/
├── resources/
├── routes/
├── storage/
├── vendor/          ← Composerでインストールした依存関係
├── .env            ← サーバー用に作成が必要
├── artisan
└── composer.json
```

### 2. Webサーバーのドキュメントルート設定

**重要**: Webサーバーのドキュメントルートを `public` ディレクトリに設定する必要があります。

#### Apache の場合（.htaccessを使用）

`public` ディレクトリに `.htaccess` ファイルがあることを確認してください。
もしない場合は、以下の内容で作成してください：

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

#### サブディレクトリに配置する場合

アプリケーションをサブディレクトリ（例: `https://purplelion51.sakura.ne.jp/cafejob/`）に配置する場合：

1. **`.env` ファイルの設定**
   ```env
   APP_URL=https://purplelion51.sakura.ne.jp/cafejob
   ```
   **重要**: 末尾にスラッシュ（`/`）は付けないでください。

2. **Webサーバーの設定（さくらサーバーの場合）**

   さくらサーバーでは、サブディレクトリの設定方法が2つあります：

   **パターンA: `/cafejob/` がプロジェクトルートを指している場合**
   - プロジェクトルートに `.htaccess` ファイルを配置（既に作成済み）
   - この `.htaccess` が自動的に `public` ディレクトリにリダイレクトします
   - **推奨**: このパターンを使用してください

   **パターンB: `/cafejob/` が既に `public` ディレクトリを指している場合**
   - プロジェクトルートの `.htaccess` は不要です（削除してください）
   - この場合、FTPでアップロードする際に、プロジェクト全体ではなく `public` ディレクトリの内容のみをアップロードする必要があります
   - **注意**: このパターンは推奨しません（セキュリティ上の理由）

3. **`.htaccess` の確認**
   - `public/.htaccess` はそのままで動作します（既に存在します）
   - プロジェクトルートの `.htaccess` はパターンAの場合のみ必要です

4. **アセットのパス**
   - Bladeテンプレートで `asset()` ヘルパーを使用している場合、自動的に正しいパスが生成されます
   - 必要に応じて `.env` に `ASSET_URL=https://purplelion51.sakura.ne.jp/cafejob` を追加

### 3. 環境設定ファイル（.env）の作成

#### 方法1: .env.exampleから作成（推奨）

1. ローカル環境で `.env.example` を `.env` にコピー
2. FTPでサーバーにアップロード
3. サーバー上で以下の値を編集：

```env
APP_NAME=CafeJob
APP_ENV=production
APP_KEY=base64:ここにアプリケーションキーを設定（後述の手順で生成）
APP_DEBUG=false
APP_URL=https://purplelion51.sakura.ne.jp/cafejob

DB_CONNECTION=mysql
DB_HOST=127.0.0.1  # サーバー提供者に確認（localhostや別のホスト名の場合あり）
DB_PORT=3306
DB_DATABASE=データベース名
DB_USERNAME=データベースユーザー名
DB_PASSWORD=データベースパスワード
```

**サブディレクトリ配置の場合の注意点：**
- `APP_URL` にはサブディレクトリを含めた完全なURLを設定してください
- 末尾にスラッシュ（`/`）は付けないでください

#### 方法2: サーバー上で直接作成

FTPクライアントで `.env` ファイルを新規作成し、上記の設定を記入してください。

**重要**: 
- `APP_KEY` は次のステップで生成します
- `APP_DEBUG` は本番環境では必ず `false` に設定してください
- `APP_URL` は `https://purplelion51.sakura.ne.jp/cafejob` に設定してください（**末尾にスラッシュなし**）
- データベース情報はサーバー提供者から提供された情報を使用してください
- サーバーによっては `DB_HOST` が `localhost` や別の値の場合があります（さくらサーバーの場合、通常は `localhost`）

### 4. アプリケーションキーの生成

#### SSHアクセスが可能な場合

サーバー上で以下のコマンドを実行：

```bash
php artisan key:generate
```

これで `.env` ファイルの `APP_KEY` が自動的に設定されます。

#### SSHアクセスができない場合（FTPのみ）

**ローカル環境で実行：**

1. ローカル環境のプロジェクトディレクトリで：
```bash
php artisan key:generate
```

2. ローカルの `.env` ファイルを開き、`APP_KEY=` の行をコピー

3. FTPでサーバーの `.env` ファイルをダウンロード

4. サーバーの `.env` ファイルの `APP_KEY=` の行を、コピーした値に置き換え

5. FTPでサーバーにアップロード

**例：**
```
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 5. ストレージとキャッシュディレクトリの権限設定

#### SSHアクセスが可能な場合

以下のコマンドを実行：

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### FTPクライアントを使用する場合

FTPクライアント（FileZilla、WinSCPなど）で以下のディレクトリの権限を設定：

**設定するディレクトリと権限：**
- `storage` → **775** または **777**
- `storage/app` → **775** または **777**
- `storage/app/public` → **775** または **777**
- `storage/framework` → **775** または **777**
- `storage/framework/cache` → **775** または **777**
- `storage/framework/sessions` → **775** または **777**
- `storage/framework/views` → **775** または **777**
- `storage/logs` → **775** または **777**
- `bootstrap/cache` → **775** または **777**

**FileZillaでの設定方法：**
1. ディレクトリを右クリック → 「ファイルの属性」を選択
2. 「数値の値」に `775` または `777` を入力
3. 「サブディレクトリに再帰的に適用」にチェック
4. 「OK」をクリック

**注意**: 権限が設定できない場合は、サーバー管理者に依頼してください。

### 6. Composer依存関係のインストール

サーバー上でSSHアクセスが可能な場合：

```bash
composer install --no-dev --optimize-autoloader
```

**注意**: `vendor` ディレクトリをローカルからアップロードした場合は、このステップは不要です。

### 7. ストレージリンクの作成

#### SSHアクセスが可能な場合

以下のコマンドを実行：

```bash
php artisan storage:link
```

これで `public/storage` が `storage/app/public` へのシンボリックリンクとして作成されます。

#### FTPクライアントのみの場合

**方法1: サーバー管理者に依頼**
- SSHで `php artisan storage:link` を実行してもらう

**方法2: 手動でシンボリックリンクを作成（サーバーが対応している場合）**
- FTPクライアントで `public/storage` を `storage/app/public` へのシンボリックリンクとして作成
- 多くのFTPクライアントではシンボリックリンクの作成ができないため、サーバー管理者に依頼することを推奨

**方法3: 一時的な回避策（推奨しない）**
- `public/storage` ディレクトリを作成し、`storage/app/public` の内容をコピー
- ただし、この方法ではアップロードされたファイルが両方の場所に保存されるため、推奨しません

### 8. キャッシュのクリアと最適化

#### SSHアクセスが可能な場合

以下のコマンドを実行してパフォーマンスを最適化：

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### FTPクライアントのみの場合

以下のディレクトリ/ファイルを削除してキャッシュをクリア：

- `bootstrap/cache/config.php`（存在する場合）
- `bootstrap/cache/routes-v7.php`（存在する場合）
- `storage/framework/cache/data/*`（すべてのファイル）
- `storage/framework/views/*`（すべてのファイル）

**注意**: キャッシュをクリアすると、次回アクセス時に自動的に再生成されます。

### 9. データベースマイグレーション

#### SSHアクセスが可能な場合

**重要**: 本番環境のデータベースにマイグレーションを実行する前に、必ずバックアップを取得してください。

```bash
php artisan migrate --force
```

#### FTPクライアントのみの場合

SSHアクセスがない場合、マイグレーションを実行できません。以下のいずれかの方法を取ってください：

**方法1: サーバー管理者に依頼**
- SSHで `php artisan migrate --force` を実行してもらう

**方法2: データベース管理ツールを使用**
- phpMyAdminなどのツールで、ローカル環境のデータベース構造を確認
- サーバーのデータベースに同じ構造を手動で作成
- **注意**: この方法は複雑でエラーが発生しやすいため、推奨しません

**方法3: 一時的にSSHアクセスを有効化**
- サーバー提供者にSSHアクセスの有効化を依頼
- マイグレーション実行後、必要に応じてSSHアクセスを無効化

### 10. 動作確認

ブラウザでアプリケーションにアクセスし、以下を確認：

- エラーページが表示されないか
- ログイン機能が動作するか
- データベース接続が正常か

## トラブルシューティング

### 500 Internal Server Error が表示される場合

1. **ログファイルを確認**
   - FTPで `storage/logs/laravel.log` をダウンロード
   - エラーメッセージを確認

2. **環境設定を確認**
   - `.env` ファイルの設定が正しいか確認
   - `APP_KEY` が設定されているか確認
   - `APP_DEBUG=true` に一時的に設定して詳細なエラーを確認（**本番環境では必ず `false` に戻す**）

3. **ディレクトリの権限を確認**
   - `storage` と `bootstrap/cache` の権限が **775** 以上になっているか確認

4. **ファイルパスを確認**
   - `vendor/autoload.php` が存在するか確認
   - `bootstrap/app.php` が存在するか確認

### ファイルが見つからないエラー（Class not found など）

1. **vendorディレクトリを確認**
   - `vendor` ディレクトリが正しくアップロードされているか確認
   - `vendor/autoload.php` が存在するか確認

2. **Composerの依存関係を再インストール**
   - SSHアクセスがある場合: `composer install --no-dev --optimize-autoloader`
   - SSHアクセスがない場合: ローカルで `composer install --no-dev` を実行し、`vendor` ディレクトリを再アップロード

### データベース接続エラー

1. **.envファイルの設定を確認**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1  # または localhost、サーバー提供者に確認
   DB_PORT=3306
   DB_DATABASE=データベース名
   DB_USERNAME=ユーザー名
   DB_PASSWORD=パスワード
   ```

2. **データベース情報の確認**
   - サーバー提供者のコントロールパネル（cPanel、Pleskなど）でデータベース情報を確認
   - データベースが作成されているか確認
   - ユーザーにデータベースへのアクセス権限があるか確認

3. **よくある問題**
   - `DB_HOST` が `127.0.0.1` ではなく `localhost` の場合がある
   - データベース名にプレフィックスが付いている場合がある（例: `username_dbname`）

### 権限エラー（Permission denied）

1. **ディレクトリの権限を確認**
   - `storage` → **775** または **777**
   - `bootstrap/cache` → **775** または **777**
   - サブディレクトリも同様に設定

2. **ファイルの権限を確認**
   - `storage/logs/laravel.log` が書き込み可能か確認

3. **サーバー管理者に確認**
   - Webサーバーのユーザー（通常は `www-data` や `apache`）が書き込み権限を持っているか確認

### 白い画面が表示される（Blank Page）

1. **エラーログを確認**
   - `storage/logs/laravel.log` を確認
   - PHPのエラーログも確認（サーバー提供者に問い合わせ）

2. **PHPのバージョンを確認**
   - Laravel 9 には PHP 8.0.2以上が必要
   - サーバーのPHPバージョンを確認

3. **必要なPHP拡張機能を確認**
   - `openssl`
   - `pdo`
   - `mbstring`
   - `tokenizer`
   - `xml`
   - `ctype`
   - `json`

### 画像やアセットが表示されない

1. **ストレージリンクを確認**
   - `public/storage` が `storage/app/public` へのシンボリックリンクになっているか確認
   - SSHで `php artisan storage:link` を実行

2. **アセットのパスを確認**
   - `public/assets` ディレクトリが正しくアップロードされているか確認
   - ブラウザの開発者ツールで404エラーを確認

## セキュリティチェックリスト

- [ ] `APP_DEBUG=false` に設定されている
- [ ] `.env` ファイルが外部からアクセスできないようになっている（.htaccessで保護）
- [ ] `storage` ディレクトリが外部から直接アクセスできないようになっている
- [ ] データベースのパスワードが強力である
- [ ] 最新のセキュリティパッチが適用されている

### .envファイルの保護

プロジェクトのルートディレクトリに `.htaccess` ファイルを作成し、以下の内容を追加してください：

```apache
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

**注意**: 既に作成したプロジェクトルートの `.htaccess` には、この設定が含まれています。

これにより、`.env` ファイルが外部から直接アクセスできなくなります。

### さくらサーバーでのデータベース設定

さくらサーバーの場合、データベース設定は以下のようになります：

```env
DB_CONNECTION=mysql
DB_HOST=localhost  # さくらサーバーでは通常 localhost
DB_PORT=3306
DB_DATABASE=データベース名  # コントロールパネルで確認
DB_USERNAME=ユーザー名  # コントロールパネルで確認
DB_PASSWORD=パスワード  # コントロールパネルで確認
```

**データベース情報の確認方法：**
1. さくらサーバーのコントロールパネルにログイン
2. 「データベース」→「MySQLデータベース」を選択
3. データベース名、ユーザー名、パスワードを確認

## 参考情報

- Laravel公式ドキュメント: https://laravel.com/docs/9.x/deployment
- サーバー提供者のドキュメントを確認して、PHPのバージョンやComposerの利用可能性を確認してください

