# vendorディレクトリの確認と対処方法

## 🔍 問題の可能性

FTPでアップロードしただけの場合、以下の問題が考えられます：

1. **`vendor` ディレクトリがアップロードされていない**
   - `vendor` ディレクトリは通常、`.gitignore` に含まれているため、Gitで管理されていない
   - FTPでアップロードする際に、`vendor` ディレクトリをアップロードし忘れた可能性

2. **`vendor` ディレクトリが不完全**
   - 一部のファイルがアップロードされていない
   - ファイルの権限が正しくない

3. **Composerの依存関係がインストールされていない**
   - サーバー上で `composer install` を実行していない

---

## 🔧 解決方法

### 方法1: vendorディレクトリをアップロード（推奨）

ローカル環境の `vendor` ディレクトリをサーバーにアップロード：

1. **ローカル環境で確認**
   - `c:\xampp\htdocs\cafejob\vendor\` ディレクトリが存在するか確認
   - `c:\xampp\htdocs\cafejob\vendor\autoload.php` が存在するか確認

2. **FTPでアップロード**
   - ローカルの `vendor` ディレクトリ全体をサーバーの `/cafejob/vendor/` にアップロード
   - **注意**: `vendor` ディレクトリは大きいので、アップロードに時間がかかります

3. **権限を設定**
   - `/cafejob/vendor/` → **755**
   - `/cafejob/vendor/autoload.php` → **644**

### 方法2: サーバー上でComposerを実行（SSHアクセスがある場合）

SSHアクセスが可能な場合：

1. **サーバーにSSHで接続**
2. **プロジェクトディレクトリに移動**
   ```bash
   cd /cafejob
   ```
3. **Composerの依存関係をインストール**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

**注意**: 
- `--no-dev` オプションで開発用パッケージを除外（本番環境用）
- `--optimize-autoloader` オプションでオートローダーを最適化

### 方法3: vendorディレクトリの確認

FTPでサーバー上の `vendor` ディレクトリを確認：

1. **`/cafejob/vendor/` ディレクトリが存在するか確認**
2. **`/cafejob/vendor/autoload.php` が存在するか確認**
3. **ディレクトリのサイズを確認**
   - `vendor` ディレクトリは通常、数十MB〜数百MBのサイズになります
   - サイズが非常に小さい場合、不完全な可能性があります

---

## 📋 確認手順

### ステップ1: ローカル環境で確認

1. **`vendor` ディレクトリが存在するか確認**
   ```
   c:\xampp\htdocs\cafejob\vendor\
   ```

2. **`autoload.php` が存在するか確認**
   ```
   c:\xampp\htdocs\cafejob\vendor\autoload.php
   ```

### ステップ2: サーバー上で確認

FTPで以下を確認：

1. **`/cafejob/vendor/` ディレクトリが存在するか**
2. **`/cafejob/vendor/autoload.php` が存在するか**
3. **ディレクトリのサイズ**

### ステップ3: アップロードまたはインストール

**パターンA: `vendor` ディレクトリが存在しない場合**
- ローカル環境の `vendor` ディレクトリをアップロード

**パターンB: `vendor` ディレクトリが存在するが不完全な場合**
- ローカル環境の `vendor` ディレクトリを再アップロード
- または、サーバー上で `composer install` を実行

**パターンC: SSHアクセスがある場合**
- サーバー上で `composer install` を実行（推奨）

---

## ✅ チェックリスト

- [ ] ローカル環境の `vendor` ディレクトリが存在する
- [ ] ローカル環境の `vendor/autoload.php` が存在する
- [ ] サーバー上の `/cafejob/vendor/` ディレクトリが存在する
- [ ] サーバー上の `/cafejob/vendor/autoload.php` が存在する
- [ ] `vendor` ディレクトリのサイズが適切（数十MB以上）
- [ ] `vendor` ディレクトリの権限が **755** に設定されている

---

## 🎯 次のアクション

1. **まず、サーバー上に `vendor` ディレクトリが存在するか確認**
2. **存在しない場合、ローカル環境の `vendor` ディレクトリをアップロード**
3. **存在するが不完全な場合、再アップロードまたは `composer install` を実行**
4. **権限を設定**
5. **再度 `/cafejob/public/index.php` にアクセス**

