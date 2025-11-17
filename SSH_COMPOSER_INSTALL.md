# SSHでComposerを実行する手順

## 🎯 目的

サーバー上で `composer install` を実行して、`vendor` ディレクトリを生成します。

## 📋 前提条件

- SSHアクセスが可能
- サーバーにComposerがインストールされている
- プロジェクトディレクトリ（`/cafejob/`）に `composer.json` が存在する

---

## 🔧 実行手順

### ステップ1: SSHでサーバーに接続

```bash
ssh username@purplelion51.sakura.ne.jp
```

**注意**: `username` は実際のユーザー名に置き換えてください。

### ステップ2: プロジェクトディレクトリに移動

```bash
cd /cafejob
```

または、さくらサーバーの場合、ホームディレクトリからの相対パス：

```bash
cd ~/cafejob
```

### ステップ3: Composerがインストールされているか確認

```bash
composer --version
```

**Composerがインストールされていない場合:**
- さくらサーバーのコントロールパネルでComposerをインストール
- または、以下のコマンドでインストール：
  ```bash
  curl -sS https://getcomposer.org/installer | php
  php composer.phar install --no-dev --optimize-autoloader
  ```

### ステップ4: Composerの依存関係をインストール

```bash
composer install --no-dev --optimize-autoloader
```

**オプションの説明:**
- `--no-dev`: 開発用パッケージを除外（本番環境用）
- `--optimize-autoloader`: オートローダーを最適化してパフォーマンスを向上

**実行時間:**
- 通常、数分〜十数分かかります
- パッケージの数によって異なります

### ステップ5: 権限を設定

```bash
chmod -R 755 vendor
chmod 644 vendor/autoload.php
```

### ステップ6: 動作確認

ブラウザで以下にアクセス：

```
https://purplelion51.sakura.ne.jp/cafejob/public/index.php
```

---

## 🔍 トラブルシューティング

### Composerが見つからない場合

**エラーメッセージ:**
```
bash: composer: command not found
```

**解決方法:**

1. **Composerのパスを確認**
   ```bash
   which composer
   ```

2. **Composerがインストールされていない場合**
   - さくらサーバーのコントロールパネルでComposerをインストール
   - または、以下のコマンドでインストール：
     ```bash
     curl -sS https://getcomposer.org/installer | php
     php composer.phar install --no-dev --optimize-autoloader
     ```

### メモリ不足エラーが発生した場合

**エラーメッセージ:**
```
Fatal error: Allowed memory size exhausted
```

**解決方法:**

```bash
php -d memory_limit=512M composer install --no-dev --optimize-autoloader
```

### 権限エラーが発生した場合

**エラーメッセージ:**
```
Permission denied
```

**解決方法:**

```bash
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

---

## ✅ 実行後の確認

### vendorディレクトリが作成されたか確認

```bash
ls -la vendor/
```

以下のファイルが存在することを確認：
- `vendor/autoload.php`
- `vendor/composer/`

### ディレクトリのサイズを確認

```bash
du -sh vendor/
```

通常、数十MB〜数百MBのサイズになります。

---

## 📋 チェックリスト

- [ ] SSHでサーバーに接続できた
- [ ] プロジェクトディレクトリ（`/cafejob/`）に移動できた
- [ ] Composerがインストールされている
- [ ] `composer.json` が存在する
- [ ] `composer install --no-dev --optimize-autoloader` を実行した
- [ ] `vendor` ディレクトリが作成された
- [ ] 権限を設定した
- [ ] ブラウザで動作確認した

---

## 🎯 次のステップ

`vendor` ディレクトリが作成されたら：

1. **プロジェクトルートの `.htaccess` を復元**
   - `.htaccess.backup` を `.htaccess` にリネーム

2. **動作確認**
   - `/cafejob/` にアクセスして確認
   - `/cafejob/public/index.php` にアクセスして確認

3. **エラーが続く場合**
   - ログファイルを確認
   - エラーメッセージを確認

