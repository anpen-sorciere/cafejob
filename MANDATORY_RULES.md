# カフェJob システム 必須ルール

## 🚨 絶対に守るべきルール

### 1. セッション管理ルール
- **cafejob_session 以外のセッション名は絶対に使用禁止**
- 他のシステム（netpgpos等）のセッション名は絶対に使用しない
- セッション開始時は必ず `session_name('cafejob_session')` を設定
- 既存セッションがある場合は必ず破棄してから新しいセッションを開始

### 2. データベースルール
- verification_code は必ず6桁（VARCHAR(6)）
- 8桁や他の桁数は絶対に使用禁止
- 住所関連のカラムは削除禁止（prefecture_id, city_id, address, postal_code）

### 3. ファイルパスルール
- **bootstrap.php を使用して絶対パスでファイルを読み込む**
- `require_once '../config/config.php'` ではなく `require_once 'bootstrap.php'` を使用
- プロジェクトルートは自動検出されるため、どこからでも同じ方法で読み込み可能
- 相対パスによる混乱を完全に防止

### 4. エラーハンドリングルール
- すべてのデータベース操作は try-catch で囲む
- エラー時はデフォルト値を設定
- 500エラーを絶対に発生させない

### 5. 認証ルール
- 店舗管理者認証は `is_shop_admin()` 関数を使用
- 認証失敗時は必ず適切なページにリダイレクト
- セッション変数は必ず `$_SESSION['shop_admin_id']` 等の形式

### 6. セキュリティルール
- 住所変更時の確認コードは画面に表示禁止
- 3回失敗でロック機能は必須
- 入力履歴は必ず記録

### 7. デバッグファイル命名ルール
- **デバッグ目的のファイルは必ず `debug_` プレフィックスを使用**
- 例: `debug_session_test.php`, `debug_database_connection.php`
- 本番用ファイルと区別しやすくするため
- 不要になったときに削除対象を明確にするため

## 📋 チェックリスト

### 新しいファイル作成時
- [ ] セッション名が `cafejob_session` になっているか
- [ ] 既存セッションを破棄しているか
- [ ] ファイルパスが正しいか
- [ ] try-catch でエラーハンドリングしているか
- [ ] データベース接続が確実か
- [ ] デバッグファイルなら `debug_` プレフィックスが付いているか

### 既存ファイル修正時
- [ ] セッション競合が発生していないか
- [ ] verification_code が6桁になっているか
- [ ] 住所関連カラムを削除していないか
- [ ] エラーハンドリングが適切か

### デバッグファイル管理
- [ ] デバッグファイルは `debug_` プレフィックスが付いているか
- [ ] 不要になったデバッグファイルは削除しているか
- [ ] 本番環境にデバッグファイルが残っていないか

## 🔧 セッション完全分離の実装

### 必須のセッション開始コード
```php
<?php
// 既存セッションを完全に破棄
if (session_status() !== PHP_SESSION_NONE) {
    session_destroy();
}

// セッション設定をリセット
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Lax');

// cafejob専用セッションを開始
session_name('cafejob_session');
session_start();

// セッションデータをクリア（必要に応じて）
// $_SESSION = array();
```

### 必須のファイル読み込みコード
```php
<?php
// どこからでも同じ方法でファイルを読み込み
require_once 'bootstrap.php';

// bootstrap.php が以下を自動的に読み込む:
// - config/config.php
// - config/database.php  
// - includes/functions.php
// - PROJECT_ROOT 定数の定義
```

### 禁止事項
- ❌ `session_name('PHPSESSID')`
- ❌ `session_name('netpgpos_session')`
- ❌ `session_name('default_session')`
- ❌ 他のシステムのセッション名
- ❌ セッション破棄なしでの新規開始
- ❌ デバッグファイルに `debug_` プレフィックスなし
- ❌ `require_once '../config/config.php'` 等の相対パス
- ❌ `require_once 'config/config.php'` 等の相対パス

## 📝 更新履歴
- 2025-10-23: 初回作成（セッション競合問題対応）
- 2025-10-23: verification_code 6桁ルール追加
- 2025-10-23: 住所関連カラム削除禁止ルール追加
- 2025-10-23: デバッグファイル命名ルール追加
- 2025-10-23: bootstrap.php による絶対パス設定追加

## ⚠️ 重要
このルールに違反した場合、システム全体が不安定になる可能性があります。
必ずこのルールを確認してから作業を開始してください。
