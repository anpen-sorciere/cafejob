# アプリケーションキー（APP_KEY）の設定方法

## 📍 現在の状況

**ローカル環境（`c:\xampp\htdocs\cafejob`）:**
- ✅ `vendor` ディレクトリが存在します
- ✅ `.env` ファイルに既に `APP_KEY` が設定されています

**既存のAPP_KEY:**
```
APP_KEY=base64:/GFkfysjP1F4Xw0yBueXhhpmtIXwBRSwB+J5m3W+NDs=
```

**新しく生成されたAPP_KEY:**
```
APP_KEY=base64:sXEb9yYtWvksVL2cOcN+VrBblKNPfwrKPjSGdF2XOD4=
```

---

## 🎯 サーバー用のAPP_KEY設定方法

### 方法1: 既存のキーを使用する（推奨）

既にローカル環境で動作しているキーを使用する場合：

1. **ローカルの `.env` ファイルを開く**
   - `c:\xampp\htdocs\cafejob\.env`

2. **APP_KEYの行をコピー**
   ```
   APP_KEY=base64:/GFkfysjP1F4Xw0yBueXhhpmtIXwBRSwB+J5m3W+NDs=
   ```

3. **サーバーの `.env` ファイルに貼り付け**
   - FTPクライアントでサーバーの `/cafejob/.env` を編集
   - `APP_KEY=` の行を上記の値に置き換え

### 方法2: 新しいキーを生成する

新しいキーを生成して使用する場合：

1. **正しいディレクトリで実行**
   ```bash
   cd c:\xampp\htdocs\cafejob
   php artisan key:generate
   ```

2. **生成されたキーを確認**
   - `.env` ファイルを開いて `APP_KEY=` の行を確認
   - または、`php artisan key:generate --show` で表示

3. **サーバーの `.env` にコピー**
   - 生成されたキーをサーバーの `.env` に設定

---

## ⚠️ 重要な注意事項

### エラーが発生した場合

**エラー: `vendor/autoload.php` が見つからない**

このエラーは、以下のいずれかが原因です：

1. **間違ったディレクトリで実行している**
   - ❌ `C:\develop\cafejob` （間違い）
   - ✅ `c:\xampp\htdocs\cafejob` （正しい）

2. **`vendor` ディレクトリが存在しない**
   - 解決方法: `composer install` を実行

### 正しいディレクトリで実行する方法

**PowerShellの場合:**
```powershell
cd c:\xampp\htdocs\cafejob
php artisan key:generate
```

**コマンドプロンプトの場合:**
```cmd
cd c:\xampp\htdocs\cafejob
php artisan key:generate
```

---

## 📋 サーバーでの設定手順（まとめ）

1. **ローカル環境でキーを確認**
   - `c:\xampp\htdocs\cafejob\.env` を開く
   - `APP_KEY=` の行をコピー

2. **サーバーの `.env` を編集**
   - FTPクライアントでサーバーの `/cafejob/.env` を開く
   - `APP_KEY=` の行を、ローカルでコピーした値に置き換え

3. **保存**
   - ファイルを保存

4. **動作確認**
   - `https://purplelion51.sakura.ne.jp/cafejob/` にアクセス
   - エラーが表示されないことを確認

---

## 🔍 トラブルシューティング

### Q: どのキーを使えばいいの？

**A:** 既存のキー（`base64:/GFkfysjP1F4Xw0yBueXhhpmtIXwBRSwB+J5m3W+NDs=`）を使用することを推奨します。
- ローカル環境で既に動作している
- 新しいキーを生成すると、既存のセッションや暗号化されたデータに影響する可能性がある

### Q: サーバーで新しいキーを生成できないの？

**A:** SSHアクセスがある場合は、サーバー上で直接生成できます：
```bash
cd /cafejob
php artisan key:generate
```

SSHアクセスがない場合は、ローカルで生成したキーをコピーしてください。

---

## ✅ 次のステップ

APP_KEYを設定したら：

1. ✅ `.env` ファイルの設定完了
2. ⏭️ ディレクトリの権限設定
3. ⏭️ データベースマイグレーション
4. ⏭️ 動作確認

