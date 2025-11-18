<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'カフェコレ（CafeColle）') - カフェコレ（CafeColle）</title>
    <meta name="description" content="@yield('description', 'コンカフェ専門の求人・集客サイト。全国のコンカフェ・メンズコンカフェから働きたい・楽しみたいお店を検索できます。')">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS (Vite経由) -->
    @vite(['resources/css/app.css'])
    <!-- 既存のカスタムCSS（必要に応じて） -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body data-cj-logged-in="{{ Auth::check() ? '1' : '0' }}">
    <!-- Header -->
    <header class="cc-header">
        <div class="cc-header-inner cc-container">
            <div class="navbar-brand m-0">
                <a href="{{ url('/') }}" class="fw-bold text-decoration-none" style="color: var(--cc-color-accent); font-size: 20px;">
                    カフェコレ
                </a>
                <div class="small text-muted">コンカフェ専門の求人・集客サイト</div>
            </div>
            <nav class="cc-header-nav d-none d-md-flex gap-2">
                <a href="{{ route('jobs.index') }}">求人を探す</a>
                <a href="{{ route('shops.index') }}">お店を探す</a>
                @if(config('feature_flags.keep', false))
                <a href="{{ route('favorites.index') }}">キープ</a>
                @endif
                @guest
                    <a href="{{ route('login') }}">ログイン</a>
                    <a href="{{ route('register') }}">会員登録</a>
                @else
                    <a href="{{ route('profile.edit') }}">マイページ</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-decoration-none" style="color: var(--cc-color-text); font-size: 14px; padding: 8px 10px !important; border-radius: 999px; background: none; border: none;">
                            ログアウト
                        </button>
                    </form>
                @endguest
            </nav>
            <!-- モバイル用メニューボタン -->
            <button class="d-md-none btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav" aria-expanded="false" aria-controls="mobileNav" style="color: var(--cc-color-text);">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <!-- モバイル用ナビゲーション -->
        <div class="collapse d-md-none" id="mobileNav">
            <div class="cc-header-inner cc-container pb-3">
                <nav class="cc-header-nav d-flex flex-column gap-2">
                    <a href="{{ route('jobs.index') }}">求人を探す</a>
                    <a href="{{ route('shops.index') }}">お店を探す</a>
                    @if(config('feature_flags.keep', false))
                    <a href="{{ route('favorites.index') }}">キープ</a>
                    @endif
                    @guest
                        <a href="{{ route('login') }}">ログイン</a>
                        <a href="{{ route('register') }}">会員登録</a>
                    @else
                        <a href="{{ route('profile.edit') }}">マイページ</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-decoration-none text-start" style="color: var(--cc-color-text); font-size: 14px; padding: 8px 10px !important; border-radius: 999px; background: none; border: none;">
                                ログアウト
                            </button>
                        </form>
                    @endguest
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-4">
        <div class="cc-container px-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Toast Container -->
    <div class="cj-toast-wrapper position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div id="cj-toast-container" class="toast-container"></div>
    </div>

    <!-- Login Modal -->
    @if(!Auth::check())
    <div class="modal fade" id="cj-login-modal" tabindex="-1" aria-labelledby="cjLoginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cjLoginModalLabel"><i class="fas fa-sign-in-alt me-2"></i>ログインが必要です</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">この機能を利用するにはログインしてください。<br>アカウントをお持ちでない方は新規登録をお願いします。</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>ログインする
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-1"></i>新規登録
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <footer class="cc-footer">
        <div class="cc-footer-inner">
            <div class="cc-footer-links mb-2">
                <a href="#">カフェコレについて</a>
                <a href="#">掲載希望の店舗様へ</a>
                <a href="#">利用規約</a>
                <a href="#">プライバシーポリシー</a>
                <a href="#">お問い合わせ</a>
            </div>
            <div>© CafeColle</div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
