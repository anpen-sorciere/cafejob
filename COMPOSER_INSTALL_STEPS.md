# Composerインストールの実行手順

## ✅ 確認済み

- Composer version 2.8.12 がインストールされている
- PHP version 8.2.20 が動作している
- Laravel 9 には PHP 8.0.2以上が必要 → ✅ 問題なし

## 🔧 次のステップ

### ステップ1: プロジェクトディレクトリに移動

```bash
cd /cafejob
```

または、さくらサーバーの場合：

```bash
cd ~/cafejob
```

### ステップ2: composer.jsonが存在するか確認

```bash
ls -la composer.json
```

`composer.json` ファイルが存在することを確認してください。

### ステップ3: Composerの依存関係をインストール

```bash
composer install --no-dev --optimize-autoloader
```

**オプションの説明:**
- `--no-dev`: 開発用パッケージを除外（本番環境用）
- `--optimize-autoloader`: オートローダーを最適化してパフォーマンスを向上

**実行時間:**
- 通常、数分〜十数分かかります
- パッケージの数によって異なります

### ステップ4: インストールが完了したら権限を設定

```bash
chmod -R 755 vendor
chmod 644 vendor/autoload.php
```

### ステップ5: vendorディレクトリが作成されたか確認

```bash
ls -la vendor/
```

以下のファイルが存在することを確認：
- `vendor/autoload.php`
- `vendor/composer/`

---

## 🔍 トラブルシューティング

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

### ネットワークエラーが発生した場合

**エラーメッセージ:**
```
Failed to download ...
```

**解決方法:**

1. **再試行**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **キャッシュをクリアして再試行**
   ```bash
   composer clear-cache
   composer install --no-dev --optimize-autoloader
   ```

---

## ✅ 実行後の確認

### vendorディレクトリが作成されたか確認

```bash
ls -la vendor/
```

### ディレクトリのサイズを確認

```bash
du -sh vendor/
```

通常、数十MB〜数百MBのサイズになります。

### autoload.phpが存在するか確認

```bash
ls -la vendor/autoload.php
```

---

## 🎯 次のステップ

`vendor` ディレクトリが作成されたら：

1. **プロジェクトルートの `.htaccess` を復元**
   - FTPで `.htaccess.backup` を `.htaccess` にリネーム

2. **動作確認**
   - `/cafejob/public/index.php` にアクセス
   - `/cafejob/` にアクセス（`.htaccess` を復元後）

3. **エラーが続く場合**
   - ログファイルを確認
   - エラーメッセージを確認

