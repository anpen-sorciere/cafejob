# 次のステップ詳細

## 現在の進捗状況

### ✅ 完了済み
1. Laravelプロジェクトのセットアップ
2. データベースマイグレーション（全テーブル）
3. Eloquentモデルの作成
4. マルチ認証システム（求職者、システム管理者、店舗管理者）
5. 基本的なダッシュボード

### 🔄 進行中
1. コントローラーの作成（JobControllerは作成済み、ビュー未作成）
2. ルーティングの設定（基本ルートは設定済み）

### ⏳ 未着手
1. ビューの移行（Bladeテンプレート化）
2. レイアウトの作成
3. 既存機能の移行

## 次のステップ（優先順位順）

### ステップ1: 基本レイアウトの作成
**目的**: 既存の`includes/layout.php`をBladeテンプレートに変換

**作成するファイル**:
- `resources/views/layouts/app.blade.php` - メインレイアウト
- `resources/views/components/navigation.blade.php` - ナビゲーション

**必要な機能**:
- ナビゲーションメニュー
- ログイン状態の表示
- フッター
- トースト通知エリア

---

### ステップ2: ホームページのビュー作成
**目的**: `pages/home.php`をBladeテンプレートに変換

**作成するファイル**:
- `resources/views/home.blade.php`

**必要な機能**:
- ヒーローセクション
- 統計データ表示
- 検索フォーム
- 人気ランキング
- 最新情報

**コントローラー**: `HomeController`（既に作成済み）

---

### ステップ3: 求人一覧・詳細ページのビュー作成
**目的**: `pages/jobs.php`と`pages/job_detail.php`をBladeテンプレートに変換

**作成するファイル**:
- `resources/views/jobs/index.blade.php` - 求人一覧
- `resources/views/jobs/show.blade.php` - 求人詳細

**必要な機能**:
- 検索・フィルター
- 求人カード表示
- ページネーション
- キープ機能（既存機能）
- 応募ボタン

**コントローラー**: `JobController`（既に作成済み）

---

### ステップ4: 認証関連ビューの調整
**目的**: 既存のログイン・登録画面を既存デザインに合わせる

**調整するファイル**:
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`

---

### ステップ5: APIエンドポイントの移行
**目的**: 既存の`api/keep_toggle.php`をLaravelのAPIルートに移行

**作成するファイル**:
- `app/Http/Controllers/Api/KeepController.php`（既に作成済み、実装が必要）

---

## 推奨実装順序

1. **基本レイアウト** → 全ページの基盤となるため最優先
2. **ホームページ** → サイトの顔となるページ
3. **求人ページ** → 主要機能の一つ
4. **認証画面の調整** → ユーザー体験の向上
5. **APIエンドポイント** → キープ機能などの動的機能

---

## 注意事項

- 既存のCSS/JSファイル（`assets/css/style.css`, `assets/js/main.js`）をそのまま使用可能
- 既存のデザインを維持しながら移行
- 段階的に実装し、各ステップで動作確認

