<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ログイン') - カフェコレ（CafeColle）</title>
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
<body style="background-color: var(--cc-color-bg);">
    <!-- ヘッダー -->
    <header class="site-header">
        <div class="container header-inner">
            {{-- 左側：ブランドロゴ＋キャッチコピー --}}
            <div class="header-brand">
                <a href="{{ url('/') }}" class="header-logo">
                    <span class="logo-mark">カフェコレ</span>
                    <span class="logo-badge">beta</span>
                </a>
                <p class="header-tagline">
                    コンカフェ専門の<span>求人・集客サイト</span>
                </p>
            </div>

            {{-- 右側：グローバルナビ --}}
            <nav class="header-nav">
                <ul class="header-menu">
                    <li><a href="{{ route('jobs.index') }}">求人を探す</a></li>
                    <li><a href="{{ route('shops.index') }}">お店を探す</a></li>
                    @if(config('feature_flags.keep', false))
                    <li><a href="{{ route('favorites.index') }}">キープ</a></li>
                    @endif
                </ul>
                <div class="header-actions">
                    @guest
                        <a href="{{ route('login') }}" class="btn-header-outline">ログイン</a>
                        <a href="{{ route('register') }}" class="btn-header-main">無料会員登録</a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="btn-header-outline">マイページ</a>
                    @endguest
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main style="padding-top: 0; min-height: calc(100vh - 200px);">
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    
    @stack('scripts')
</body>
</html>

