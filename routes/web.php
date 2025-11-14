<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CastController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ShopAdminLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ホーム
Route::get('/', [HomeController::class, 'index'])->name('home');

// 求人関連
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');

// 応募関連
Route::middleware('auth')->group(function () {
    Route::get('/applications', [App\Http\Controllers\ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications', [App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');
    Route::post('/applications/{id}/cancel', [App\Http\Controllers\ApplicationController::class, 'cancel'])->name('applications.cancel');
});

// チャット関連（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/show', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show.id');
    Route::post('/chat/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.send');
});

// キープ一覧（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/bulk-apply', [App\Http\Controllers\FavoriteController::class, 'bulkApply'])->name('favorites.bulk-apply');
});

// 店舗関連
Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{id}', [ShopController::class, 'show'])->name('shops.show');

// 口コミ機能
Route::middleware('auth')->group(function () {
    Route::post('/shops/{shop}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
});

// キャスト関連
Route::get('/casts', [CastController::class, 'index'])->name('casts.index');
Route::get('/casts/{id}', [CastController::class, 'show'])->name('casts.show');

// 最新情報
Route::get('/updates', [UpdateController::class, 'index'])->name('updates.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/images', [ProfileController::class, 'updateImages'])->name('profile.update-images');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// 管理者ログイン
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/shops', [App\Http\Controllers\Admin\AdminShopController::class, 'index'])->name('admin.shops.index');
        Route::get('/shops/{id}', [App\Http\Controllers\Admin\AdminShopController::class, 'show'])->name('admin.shops.show');
        Route::post('/shops/{id}/status', [App\Http\Controllers\Admin\AdminShopController::class, 'updateStatus'])->name('admin.shops.update-status');
        Route::get('/jobs', [App\Http\Controllers\Admin\AdminJobController::class, 'index'])->name('admin.jobs.index');
        Route::get('/jobs/{id}', [App\Http\Controllers\Admin\AdminJobController::class, 'show'])->name('admin.jobs.show');
        Route::post('/jobs/{id}/status', [App\Http\Controllers\Admin\AdminJobController::class, 'updateStatus'])->name('admin.jobs.update-status');
        Route::get('/users', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}', [App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('/users/{id}/status', [App\Http\Controllers\Admin\AdminUserController::class, 'updateStatus'])->name('admin.users.update-status');
        Route::get('/applications', [App\Http\Controllers\Admin\AdminApplicationController::class, 'index'])->name('admin.applications.index');
        Route::get('/applications/{id}', [App\Http\Controllers\Admin\AdminApplicationController::class, 'show'])->name('admin.applications.show');
        Route::post('/applications/{id}/status', [App\Http\Controllers\Admin\AdminApplicationController::class, 'updateStatus'])->name('admin.applications.update-status');
        
        // 求職者報告管理
        Route::get('/user-reports', [App\Http\Controllers\Admin\AdminUserReportController::class, 'index'])->name('admin.user-reports.index');
        Route::get('/user-reports/{id}', [App\Http\Controllers\Admin\AdminUserReportController::class, 'show'])->name('admin.user-reports.show');
        Route::post('/user-reports/{id}/update-status', [App\Http\Controllers\Admin\AdminUserReportController::class, 'updateStatus'])->name('admin.user-reports.update-status');
        
        // 口コミ管理
        Route::get('/reviews', [App\Http\Controllers\Admin\AdminReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('/reviews/{id}', [App\Http\Controllers\Admin\AdminReviewController::class, 'show'])->name('admin.reviews.show');
        Route::post('/reviews/{id}/approve', [App\Http\Controllers\Admin\AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::post('/reviews/{id}/reject', [App\Http\Controllers\Admin\AdminReviewController::class, 'reject'])->name('admin.reviews.reject');
        
        // 店舗管理者作成
        Route::get('/shop-admins/create', [App\Http\Controllers\Admin\AdminShopAdminController::class, 'create'])->name('admin.shop-admins.create');
        Route::post('/shop-admins', [App\Http\Controllers\Admin\AdminShopAdminController::class, 'store'])->name('admin.shop-admins.store');
        
        // 画像監視
        Route::get('/image-monitoring', [App\Http\Controllers\Admin\AdminImageMonitoringController::class, 'index'])->name('admin.image-monitoring.index');
        Route::delete('/images/{id}', [App\Http\Controllers\Admin\AdminDeleteImageController::class, 'destroy'])->name('admin.images.destroy');
        
        // 検証試行管理
        Route::get('/verification-attempts', [App\Http\Controllers\Admin\AdminVerificationAttemptController::class, 'index'])->name('admin.verification-attempts.index');
        
        // サンプルデータ生成
        Route::get('/sample-data', [App\Http\Controllers\Admin\AdminSampleDataController::class, 'index'])->name('admin.sample-data.index');
        Route::post('/sample-data', [App\Http\Controllers\Admin\AdminSampleDataController::class, 'store'])->name('admin.sample-data.store');
    });
});

// 店舗登録
Route::get('/shop-register', [App\Http\Controllers\ShopRegisterController::class, 'create'])->name('shop-register.create');
Route::post('/shop-register', [App\Http\Controllers\ShopRegisterController::class, 'store'])->name('shop-register.store');

// 店舗管理者ログイン
Route::prefix('shop-admin')->group(function () {
    Route::get('/login', [ShopAdminLoginController::class, 'showLoginForm'])->name('shop-admin.login');
    Route::post('/login', [ShopAdminLoginController::class, 'login']);
    Route::post('/logout', [ShopAdminLoginController::class, 'logout'])->name('shop-admin.logout');
    
    Route::middleware(['auth:shop_admin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ShopAdmin\ShopDashboardController::class, 'index'])->name('shop-admin.dashboard');
        Route::get('/applications', [App\Http\Controllers\ShopAdmin\ApplicationController::class, 'index'])->name('shop-admin.applications.index');
        Route::get('/applications/{id}', [App\Http\Controllers\ShopAdmin\ApplicationController::class, 'show'])->name('shop-admin.applications.show');
        Route::post('/applications/{id}/status', [App\Http\Controllers\ShopAdmin\ApplicationController::class, 'updateStatus'])->name('shop-admin.applications.update-status');
        Route::post('/applications/{id}/report', [App\Http\Controllers\ShopAdmin\ApplicationController::class, 'report'])->name('shop-admin.applications.report');
        Route::post('/applications/{id}/ban', [App\Http\Controllers\ShopAdmin\ApplicationController::class, 'ban'])->name('shop-admin.applications.ban');
        Route::get('/jobs', [App\Http\Controllers\ShopAdmin\JobController::class, 'index'])->name('shop-admin.jobs.index');
        Route::get('/jobs/{id}', [App\Http\Controllers\ShopAdmin\JobController::class, 'show'])->name('shop-admin.jobs.show');
        Route::get('/jobs/{id}/edit', [App\Http\Controllers\ShopAdmin\JobController::class, 'edit'])->name('shop-admin.jobs.edit');
        Route::put('/jobs/{id}', [App\Http\Controllers\ShopAdmin\JobController::class, 'update'])->name('shop-admin.jobs.update');
        Route::get('/chat', [App\Http\Controllers\ShopAdmin\ChatController::class, 'index'])->name('shop-admin.chat.index');
        Route::get('/chat/show', [App\Http\Controllers\ShopAdmin\ChatController::class, 'show'])->name('shop-admin.chat.show');
        Route::get('/chat/{id}', [App\Http\Controllers\ShopAdmin\ChatController::class, 'show'])->name('shop-admin.chat.show.id');
        Route::post('/chat/{id}', [App\Http\Controllers\ShopAdmin\ChatController::class, 'show'])->name('shop-admin.chat.send');
        Route::get('/shop-info', [App\Http\Controllers\ShopAdmin\ShopInfoController::class, 'edit'])->name('shop-admin.shop-info');
        Route::put('/shop-info', [App\Http\Controllers\ShopAdmin\ShopInfoController::class, 'update'])->name('shop-admin.shop-info.update');
        
        // 求人作成
        Route::get('/jobs/create', [App\Http\Controllers\ShopAdmin\JobController::class, 'create'])->name('shop-admin.jobs.create');
        Route::post('/jobs', [App\Http\Controllers\ShopAdmin\JobController::class, 'store'])->name('shop-admin.jobs.store');
        
        // キャスト管理
        Route::get('/cast-management', [App\Http\Controllers\ShopAdmin\CastManagementController::class, 'index'])->name('shop-admin.cast-management.index');
        Route::post('/cast-management', [App\Http\Controllers\ShopAdmin\CastManagementController::class, 'store'])->name('shop-admin.cast-management.store');
        Route::put('/cast-management/{id}', [App\Http\Controllers\ShopAdmin\CastManagementController::class, 'update'])->name('shop-admin.cast-management.update');
        Route::delete('/cast-management/{id}', [App\Http\Controllers\ShopAdmin\CastManagementController::class, 'destroy'])->name('shop-admin.cast-management.destroy');
    });
});
