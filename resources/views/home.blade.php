@extends('layouts.app')

@section('title', 'ホーム')
@section('description', 'コンカフェ専門の求人・集客サイト。全国のコンカフェ・メンズコンカフェから働きたい・楽しみたいお店を検索できます。')

@section('content')
<!-- ヒーローセクション -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 text-center mb-5">
                <h1 class="hero-title mb-3">カフェコレ（CafeColle）</h1>
                <h2 class="hero-subtitle-large mb-4">コンカフェ専門の求人・集客サイト</h2>
                <p class="hero-description mb-4">
                    全国のコンカフェ・メンズコンカフェから「働きたい」「楽しみたい」<br>
                    お店のエリアから検索できるコンカフェ専門のポータルサイトです。
                </p>
            </div>
        </div>
        
        <!-- 統計情報 -->
        <div class="row mb-5">
            <div class="col-md-3 col-6 mb-3">
                <div class="stats-card-modern">
                    <div class="stats-icon"><i class="fas fa-store"></i></div>
                    <div class="stats-number-modern">{{ number_format($stats['total_shops']) }}</div>
                    <div class="stats-label-modern">掲載店舗</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stats-card-modern">
                    <div class="stats-icon"><i class="fas fa-users"></i></div>
                    <div class="stats-number-modern">{{ number_format($stats['total_casts']) }}</div>
                    <div class="stats-label-modern">キャスト</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stats-card-modern">
                    <div class="stats-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stats-number-modern">{{ number_format($stats['total_jobs']) }}</div>
                    <div class="stats-label-modern">アルバイト求人</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stats-card-modern">
                    <div class="stats-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stats-number-modern">{{ number_format($stats['total_applications']) }}</div>
                    <div class="stats-label-modern">応募数</div>
                </div>
            </div>
        </div>
        
        <!-- CTAボタン -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('jobs.index') }}" class="btn btn-hero btn-lg">
                        <i class="fas fa-briefcase me-2"></i>求人を探す
                    </a>
                    <a href="{{ route('shops.index') }}" class="btn btn-hero-outline btn-lg">
                        <i class="fas fa-store me-2"></i>お店を探す
                    </a>
                    @auth
                        @php
                            $unread_count = 0; // TODO: 未読メッセージ数の取得を実装
                        @endphp
                        @if($unread_count > 0)
                            <a href="{{ route('chat.index') }}" class="btn btn-hero-warning btn-lg">
                                <i class="fas fa-comments me-2"></i>未読メッセージ
                                <span class="badge bg-danger ms-2">{{ $unread_count }}</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 検索セクション -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="search-form-modern">
            <h3 class="text-center mb-4">
                <i class="fas fa-search me-2 text-primary"></i>キーワードで店舗を検索
            </h3>
            <form method="GET" action="{{ route('jobs.index') }}" class="search-form-inline">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="keyword" class="form-label fw-bold">キーワード</label>
                        <input type="text" class="form-control form-control-lg" id="keyword" name="keyword" 
                               placeholder="店舗名、職種、エリアなど" value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="prefecture" class="form-label fw-bold">都道府県</label>
                        <select class="form-select form-select-lg" id="prefecture" name="prefecture">
                            <option value="">すべて</option>
                            @foreach(\App\Models\Prefecture::orderBy('id')->get() as $prefecture)
                                <option value="{{ $prefecture->id }}" {{ request('prefecture') == $prefecture->id ? 'selected' : '' }}>
                                    {{ $prefecture->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="concept_type" class="form-label fw-bold">ジャンル</label>
                        <select class="form-select form-select-lg" id="concept_type" name="concept_type">
                            <option value="">すべて</option>
                            <option value="maid" {{ request('concept_type') == 'maid' ? 'selected' : '' }}>メイドカフェ</option>
                            <option value="butler" {{ request('concept_type') == 'butler' ? 'selected' : '' }}>執事喫茶</option>
                            <option value="idol" {{ request('concept_type') == 'idol' ? 'selected' : '' }}>アイドルカフェ</option>
                            <option value="cosplay" {{ request('concept_type') == 'cosplay' ? 'selected' : '' }}>コスプレカフェ</option>
                            <option value="other" {{ request('concept_type') == 'other' ? 'selected' : '' }}>その他</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="{{ route('jobs.index') }}" class="text-decoration-none">
                    <i class="fas fa-filter me-1"></i>条件を指定して店舗を検索
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 人気ランキングセクション -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-trophy me-2 text-warning"></i>人気求人ランキング
                </h3>
                @if($popular_jobs->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">まだ求人情報がありません</p>
                    </div>
                @else
                    @foreach($popular_jobs as $index => $job)
                        <div class="ranking-item">
                            <div class="ranking-number">{{ $index + 1 }}</div>
                            <div class="ranking-content">
                                <div class="ranking-title">
                                    <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none">
                                        {{ $job->title }}
                                    </a>
                                </div>
                                <div class="ranking-subtitle">
                                    {{ $job->shop->name }} | 
                                    {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="text-center mt-3">
                    <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary">
                        すべての求人を見る <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-star me-2 text-warning"></i>人気店舗ランキング
                </h3>
                @if($popular_shops->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">まだ店舗情報がありません</p>
                    </div>
                @else
                    @foreach($popular_shops as $index => $shop)
                        <div class="ranking-item">
                            <div class="ranking-number">{{ $index + 1 }}</div>
                            <div class="ranking-content">
                                <div class="ranking-title">
                                    <a href="#" class="text-decoration-none">
                                        {{ $shop->name }}
                                    </a>
                                </div>
                                <div class="ranking-subtitle">
                                    {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }} | 
                                    <span class="badge badge-concept">{{ $shop->concept_type }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="text-center mt-3">
                    <a href="{{ route('shops.index') }}" class="btn btn-outline-primary">
                        すべてのお店を見る <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 最新情報セクション -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-clock me-2"></i>最新の求人情報
                </h3>
                <div class="row">
                    @if($latest_jobs->isEmpty())
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">まだ求人情報がありません</p>
                        </div>
                    @else
                        @foreach($latest_jobs as $job)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 job-card-modern">
                                    @if($job->shop->image_url)
                                        <img src="{{ $job->shop->image_url }}" 
                                             class="card-img-top" alt="{{ $job->shop->name }}"
                                             loading="lazy">
                                    @else
                                        {{-- 画像がない場合はプレースホルダー背景を表示 --}}
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 180px;">
                                            <i class="fas fa-store fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none">
                                                {{ $job->title }}
                                            </a>
                                        </h6>
                                        <p class="card-text small mb-2">
                                            <i class="fas fa-store me-1"></i>{{ $job->shop->name }}<br>
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <span class="text-muted">
                                                {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}
                                            </span>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $job->created_at->diffForHumans() }}
                                            </small>
                                            @if($job->salary_min)
                                                <span class="badge badge-salary">
                                                    <i class="fas fa-yen-sign me-1"></i>{{ number_format($job->salary_min) }}円〜
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <div class="col-lg-6 mb-5">
                <h3 class="mb-4">
                    <i class="fas fa-store me-2"></i>最新のお店情報
                </h3>
                <div class="row">
                    @if($latest_shops->isEmpty())
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">まだ店舗情報がありません</p>
                        </div>
                    @else
                        @foreach($latest_shops as $shop)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 shop-card-modern">
                                    @if($shop->image_url)
                                        <img src="{{ $shop->image_url }}" 
                                             class="card-img-top" alt="{{ $shop->name }}"
                                             loading="lazy">
                                    @else
                                        {{-- 画像がない場合はプレースホルダー背景を表示 --}}
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 180px;">
                                            <i class="fas fa-store fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('shops.show', $shop->id) }}" class="text-decoration-none">
                                                {{ $shop->name }}
                                            </a>
                                        </h6>
                                        <p class="card-text small">
                                            {{ $shop->description ? \Illuminate\Support\Str::limit($shop->description, 50) . '...' : '' }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}
                                            </small>
                                            <span class="badge badge-concept">
                                                @if($shop->concept_type == 'maid')
                                                    メイドカフェ
                                                @elseif($shop->concept_type == 'butler')
                                                    執事喫茶
                                                @elseif($shop->concept_type == 'idol')
                                                    アイドルカフェ
                                                @elseif($shop->concept_type == 'cosplay')
                                                    コスプレカフェ
                                                @else
                                                    その他
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA セクション -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h3 class="mb-3">今すぐ始めよう！</h3>
        <p class="mb-4">理想のコンカフェで働く、またはお気に入りのお店を見つけよう</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>無料会員登録
                </a>
            @endguest
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-briefcase me-2"></i>求人を探す
            </a>
            <a href="{{ route('shops.index') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-store me-2"></i>お店を探す
            </a>
        </div>
    </div>
</section>
@endsection

