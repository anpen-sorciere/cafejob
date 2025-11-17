# 405エラー（移行後）の解決方法

## 🔍 現在の状況

- `/cafejob/` → **405 Method Not Allowed** エラー ❌
- Laravelは起動しようとしている（500エラーではない）

## 📋 問題の原因

405エラーは、以下の原因が考えられます：

1. **`index.php`のパス修正が不完全**
   - `index.php`のパスが正しく修正されていない可能性

2. **`.htaccess`の設定が正しくない**
   - `baseball_slg`と同じ`.htaccess`になっているか確認

3. **ルーティングの問題**
   - Laravelのルーティングが正しく動作していない

---

## 🔧 解決方法

### ステップ1: index.phpのパス修正を確認

SSHで以下を実行：

```bash
cd /home/purplelion51/www/cafejob
cat index.php | grep -E "(vendor|bootstrap|storage)"
```

以下のように表示されるはずです：

```
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
```

**もし `../` が含まれている場合、修正が必要です。**

### ステップ2: index.phpを直接編集

```bash
vi index.php
```

または：

```bash
nano index.php
```

以下の3箇所を確認・修正：

1. **19行目付近:**
   ```php
   // 正しい内容
   if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
   ```

2. **34行目付近:**
   ```php
   // 正しい内容
   require __DIR__.'/vendor/autoload.php';
   ```

3. **47行目付近:**
   ```php
   // 正しい内容
   $app = require_once __DIR__.'/bootstrap/app.php';
   ```

### ステップ3: .htaccessの内容を確認

```bash
cat .htaccess
```

`baseball_slg`と同じ内容になっているか確認してください。

### ステップ4: ログファイルを確認

```bash
tail -50 storage/logs/laravel.log
```

エラーメッセージの詳細を確認してください。

### ステップ5: 一時的にデバッグモードを有効化

`.env`ファイルで：

```bash
vi .env
```

または：

```bash
nano .env
```

以下を変更：

```env
APP_DEBUG=true
```

これで詳細なエラーメッセージが表示されます。

**重要**: デバッグ後は必ず `APP_DEBUG=false` に戻してください。

---

## 🔍 確認事項

### 1. index.phpのパスが正しいか確認

```bash
cd /home/purplelion51/www/cafejob
grep -n "\.\./" index.php
```

`../` が含まれている行があれば、修正が必要です。

### 2. .htaccessの内容を確認

```bash
cat .htaccess
```

`baseball_slg`と同じ内容になっているか確認してください。

### 3. ファイルの存在確認

```bash
ls -la index.php
ls -la vendor/autoload.php
ls -la bootstrap/app.php
```

すべてのファイルが存在するか確認してください。

---

## ✅ チェックリスト

- [ ] `index.php`のパスが正しく修正されている（`../`が含まれていない）
- [ ] `.htaccess`が`baseball_slg`と同じ内容になっている
- [ ] `vendor/autoload.php`が存在する
- [ ] `bootstrap/app.php`が存在する
- [ ] ログファイルを確認した
- [ ] 一時的に`APP_DEBUG=true`に設定して詳細なエラーを確認した

---

## 🎯 次のアクション

1. **`index.php`のパス修正を確認**
2. **ログファイルを確認**
3. **一時的に`APP_DEBUG=true`に設定して詳細なエラーを確認**

