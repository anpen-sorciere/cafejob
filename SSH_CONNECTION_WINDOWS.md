# WindowsでSSH接続する方法

## 🖥️ WindowsでのSSH接続方法

Windowsでは、以下のツールを使用してSSH接続できます：

1. **PowerShell**（推奨・Windows 10以降に標準搭載）
2. **コマンドプロンプト（CMD）**
3. **Git Bash**（Git for Windowsに含まれる）
4. **PuTTY**（専用SSHクライアント）

---

## 🔧 方法1: PowerShellを使用（推奨）

### ステップ1: PowerShellを起動

1. **Windowsキーを押して「PowerShell」と入力**
2. **「Windows PowerShell」を選択**

または：

1. **Windowsキー + X**
2. **「Windows PowerShell」を選択**

### ステップ2: SSHでサーバーに接続

```powershell
ssh username@purplelion51.sakura.ne.jp
```

**注意**: `username` は実際のユーザー名に置き換えてください。

**初回接続時:**
- 「ホストの真正性を確認できません」というメッセージが表示されます
- `yes` と入力してEnterキーを押してください

### ステップ3: パスワードを入力

パスワードの入力が求められます：
- パスワードを入力（画面には表示されません）
- Enterキーを押してください

### ステップ4: プロジェクトディレクトリに移動

```bash
cd /cafejob
```

または、さくらサーバーの場合：

```bash
cd ~/cafejob
```

### ステップ5: Composerを実行

```bash
composer install --no-dev --optimize-autoloader
```

---

## 🔧 方法2: コマンドプロンプト（CMD）を使用

### ステップ1: コマンドプロンプトを起動

1. **Windowsキーを押して「cmd」と入力**
2. **「コマンドプロンプト」を選択**

または：

1. **Windowsキー + R**
2. **「cmd」と入力してEnter**

### ステップ2: SSHでサーバーに接続

```bash
ssh username@purplelion51.sakura.ne.jp
```

**注意**: Windows 10以降では、SSHクライアントが標準搭載されています。
Windows 7/8の場合は、PuTTYなどの専用ツールが必要です。

### ステップ3以降: PowerShellと同じ手順

---

## 🔧 方法3: PuTTYを使用

### ステップ1: PuTTYをダウンロード・インストール

1. **PuTTYの公式サイトからダウンロード**
   - https://www.putty.org/

2. **インストール**

### ステップ2: PuTTYを起動

1. **PuTTYを起動**

2. **接続情報を入力**
   - **Host Name**: `purplelion51.sakura.ne.jp`
   - **Port**: `22`（デフォルト）
   - **Connection type**: `SSH`

3. **「Open」をクリック**

### ステップ3: ログイン

1. **ユーザー名を入力**
   ```
   username
   ```

2. **パスワードを入力**
   - パスワードを入力（画面には表示されません）

### ステップ4: プロジェクトディレクトリに移動

```bash
cd /cafejob
```

### ステップ5: Composerを実行

```bash
composer install --no-dev --optimize-autoloader
```

---

## 📋 さくらサーバーのSSH接続情報

さくらサーバーのSSH接続情報は、コントロールパネルで確認できます：

1. **さくらサーバーのコントロールパネルにログイン**
2. **「サーバーの設定」→「SSH」を選択**
3. **SSH接続情報を確認**
   - ホスト名
   - ポート番号（通常は22）
   - ユーザー名

---

## ✅ 推奨される方法

**PowerShellを使用することを推奨します。**

理由：
- Windows 10以降に標準搭載されている
- 追加のソフトウェアが不要
- 使いやすい

---

## 🔍 トラブルシューティング

### SSHコマンドが見つからない場合

**エラーメッセージ:**
```
'ssh' は、内部コマンドまたは外部コマンド、操作可能なプログラムまたはバッチ ファイルとして認識されていません。
```

**解決方法:**

1. **Windows 10以降の場合**
   - 「設定」→「アプリ」→「オプション機能」→「OpenSSH クライアント」をインストール

2. **Windows 7/8の場合**
   - PuTTYなどの専用SSHクライアントを使用

### 接続が拒否される場合

**エラーメッセージ:**
```
Connection refused
```

**解決方法:**

1. **SSHが有効になっているか確認**
   - さくらサーバーのコントロールパネルでSSHを有効化

2. **ポート番号を確認**
   - 通常は22ですが、変更されている場合があります

3. **ファイアウォールの設定を確認**

---

## 🎯 次のステップ

SSH接続が成功したら：

1. **プロジェクトディレクトリに移動**
   ```bash
   cd /cafejob
   ```

2. **Composerを実行**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **権限を設定**
   ```bash
   chmod -R 755 vendor
   ```

4. **動作確認**
   - ブラウザで `/cafejob/public/index.php` にアクセス

