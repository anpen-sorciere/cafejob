# マルチ認証システム実装完了

## ✅ 実装内容

### 1. アカウント種別

- **求職者（User）**: `users`テーブル - 標準のLaravel Breeze認証
- **システム管理者（Admin）**: `admins`テーブル - 専用認証ガード
- **店舗運営者（ShopAdmin）**: `shop_admins`テーブル - 専用認証ガード

### 2. 作成したファイル

#### モデル
- `app/Models/Admin.php` - システム管理者モデル
- `app/Models/ShopAdmin.php` - 店舗管理者モデル

#### コントローラー
- `app/Http/Controllers/Auth/AdminLoginController.php` - 管理者ログイン
- `app/Http/Controllers/Auth/ShopAdminLoginController.php` - 店舗管理者ログイン

#### ミドルウェア
- `app/Http/Middleware/EnsureAdmin.php` - 管理者認証チェック
- `app/Http/Middleware/EnsureShopAdmin.php` - 店舗管理者認証チェック

#### ビュー
- `resources/views/auth/admin-login.blade.php` - 管理者ログイン画面
- `resources/views/auth/shop-admin-login.blade.php` - 店舗管理者ログイン画面

### 3. 設定ファイル

#### `config/auth.php`
- `admin`ガード追加
- `shop_admin`ガード追加
- 各ガード用のプロバイダー設定

#### `routes/web.php`
- `/admin/login` - 管理者ログイン
- `/admin/dashboard` - 管理者ダッシュボード（認証必須）
- `/shop-admin/login` - 店舗管理者ログイン
- `/shop-admin/dashboard` - 店舗管理者ダッシュボード（認証必須）

### 4. 認証方法

#### 求職者
```php
Auth::guard('web')->check(); // または Auth::check()
Auth::guard('web')->user(); // または Auth::user()
```

#### システム管理者
```php
Auth::guard('admin')->check();
Auth::guard('admin')->user();
```

#### 店舗管理者
```php
Auth::guard('shop_admin')->check();
Auth::guard('shop_admin')->user();
```

### 5. ログインURL

- **求職者**: `http://localhost:8000/login`
- **システム管理者**: `http://localhost:8000/admin/login`
- **店舗管理者**: `http://localhost:8000/shop-admin/login`

### 6. パスワード認証

既存の`password_hash`カラムを使用しています。`Hash::check()`で検証します。

### 7. 次のステップ

1. 管理者ダッシュボードの実装
2. 店舗管理者ダッシュボードの実装
3. 各ユーザータイプ用のナビゲーションメニュー
4. 権限管理の実装（super_admin, admin, moderator）

