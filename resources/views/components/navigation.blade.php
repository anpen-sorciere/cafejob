<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="fas fa-coffee me-2"></i>カフェコレ（CafeColle）
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i>ホーム
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">
                        <i class="fas fa-briefcase me-1"></i>求人検索
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shops.*') ? 'active' : '' }}" href="{{ route('shops.index') }}">
                        <i class="fas fa-store me-1"></i>お店検索
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('casts.*') ? 'active' : '' }}" href="{{ route('casts.index') }}">
                        <i class="fas fa-users me-1"></i>キャスト
                    </a>
                </li>
                @if(config('feature_flags.keep', false))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                        <i class="fas fa-bookmark me-1"></i>キープ
                    </a>
                </li>
                @endif
            </ul>
            
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->username }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2"></i>プロフィール</a></li>
                            <li><a class="dropdown-item" href="{{ route('applications.index') }}"><i class="fas fa-file-alt me-2"></i>応募履歴</a></li>
                            @if(config('feature_flags.keep', false))
                            <li><a class="dropdown-item" href="{{ route('favorites.index') }}"><i class="fas fa-bookmark me-2"></i>キープ一覧</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>ログイン
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>新規登録
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

