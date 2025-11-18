@extends('layouts.app')

@section('title', 'ホーム')
@section('description', 'コンカフェ専門の求人・集客サイト。全国のコンカフェ・メンズコンカフェから働きたい・楽しみたいお店を検索できます。')

@section('content')
<!-- ファーストビュー（グラデーション背景） -->
<div class="cc-hero-section">
    <div class="cc-container position-relative" style="z-index: 1;">
        <div class="cc-card">
            <h1 class="h3 mb-2">カフェコレ（CafeColle）</h1>
            <h2 class="h5 mb-3 text-muted">コンカフェ専門の求人・集客サイト</h2>
            <p class="mb-4">
                全国のコンカフェ・メンズコンカフェから「働きたい」「楽しみたい」<br>
                お店のエリアから検索できるコンカフェ専門のポータルサイトです。
            </p>
            
            <!-- 統計情報（白カード横並び） -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="cc-stat-card">
                        <div class="cc-stat-card-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="cc-stat-card-content">
                            <div class="cc-stat-card-number">{{ number_format($stats['total_shops']) }}</div>
                            <div class="cc-stat-card-label">掲載店舗</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="cc-stat-card">
                        <div class="cc-stat-card-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="cc-stat-card-content">
                            <div class="cc-stat-card-number">{{ number_format($stats['total_casts']) }}</div>
                            <div class="cc-stat-card-label">キャスト</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="cc-stat-card">
                        <div class="cc-stat-card-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="cc-stat-card-content">
                            <div class="cc-stat-card-number">{{ number_format($stats['total_jobs']) }}</div>
                            <div class="cc-stat-card-label">アルバイト求人</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="cc-stat-card">
                        <div class="cc-stat-card-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="cc-stat-card-content">
                            <div class="cc-stat-card-number">{{ number_format($stats['total_applications']) }}</div>
                            <div class="cc-stat-card-label">応募数</div>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- 検索フォーム -->
        <h3 class="h5 mt-4 mb-3">キーワードで店舗を検索</h3>
        <form method="GET" action="{{ route('jobs.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="keyword" class="form-label fw-bold">キーワード</label>
                    <input type="text" class="form-control" id="keyword" name="keyword" 
                           placeholder="店舗名、職種、エリアなど" value="{{ request('keyword') }}">
                </div>
                <div class="col-md-3">
                    <label for="prefecture" class="form-label fw-bold">都道府県</label>
                    <select class="form-select" id="prefecture" name="prefecture">
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
                    <select class="form-select" id="concept_type" name="concept_type">
                        <option value="">すべて</option>
                        <option value="maid" {{ request('concept_type') == 'maid' ? 'selected' : '' }}>メイドカフェ</option>
                        <option value="butler" {{ request('concept_type') == 'butler' ? 'selected' : '' }}>執事喫茶</option>
                        <option value="idol" {{ request('concept_type') == 'idol' ? 'selected' : '' }}>アイドルカフェ</option>
                        <option value="cosplay" {{ request('concept_type') == 'cosplay' ? 'selected' : '' }}>コスプレカフェ</option>
                        <option value="other" {{ request('concept_type') == 'other' ? 'selected' : '' }}>その他</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
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

<!-- 人気ランキングセクション -->
<div class="cc-container mb-4">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="cc-card">
                <h3 class="h5 mb-4">
                    <i class="fas fa-trophy me-2" style="color: var(--cc-color-accent);"></i>人気求人ランキング
                </h3>
                @if($popular_jobs->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">まだ求人情報がありません</p>
                    </div>
                @else
                    @foreach($popular_jobs as $index => $job)
                        <div class="cc-job-card position-relative mb-3">
                            <span class="cc-ranking-badge">No.{{ $index + 1 }}</span>
                            <div>
                                <img src="{{ $job->shop->image_url ?? asset('assets/images/CafeColle_NoImage.png') }}" 
                                     class="cc-job-thumb" 
                                     alt="{{ $job->shop->name }}"
                                     loading="lazy">
                            </div>
                            <div class="flex-grow-1">
                                <div class="cc-job-title mb-1">
                                    <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none" style="color: inherit;">
                                        {{ $job->title }}
                                    </a>
                                </div>
                                <div class="cc-job-meta mb-2">
                                    {{ $job->shop->name }} ｜ 
                                    {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}
                                    @if($job->salary_min)
                                        ｜ 時給 {{ number_format($job->salary_min) }}円〜
                                        @if($job->salary_max)
                                            {{ number_format($job->salary_max) }}円
                                        @endif
                                    @endif
                                </div>
                                @if($job->shop->concept_type)
                                    <div class="mb-2">
                                        <span class="cc-tag">{{ $job->shop->concept_type }}</span>
                                    </div>
                                @endif
                                <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-primary btn-sm">
                                    詳細を見る
                                </a>
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
        </div>
        
        <div class="col-lg-6">
            <div class="cc-card">
                <h3 class="h5 mb-4">
                    <i class="fas fa-star me-2" style="color: var(--cc-color-accent);"></i>人気店舗ランキング
                </h3>
                @if($popular_shops->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">まだ店舗情報がありません</p>
                    </div>
                @else
                    @foreach($popular_shops as $index => $shop)
                        <div class="cc-job-card position-relative mb-3">
                            <span class="cc-ranking-badge">No.{{ $index + 1 }}</span>
                            <div>
                                <img src="{{ $shop->image_url ?? asset('assets/images/CafeColle_NoImage.png') }}" 
                                     class="cc-job-thumb" 
                                     alt="{{ $shop->name }}"
                                     loading="lazy">
                            </div>
                            <div class="flex-grow-1">
                                <div class="cc-job-title mb-1">
                                    <a href="{{ route('shops.show', $shop->id) }}" class="text-decoration-none" style="color: inherit;">
                                        {{ $shop->name }}
                                    </a>
                                </div>
                                <div class="cc-job-meta mb-2">
                                    {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}
                                    @if($shop->reviews_avg_rating)
                                        ｜ 評価 {{ number_format($shop->reviews_avg_rating, 1) }}
                                    @endif
                                    @if($shop->jobs_count > 0)
                                        ｜ 求人 {{ $shop->jobs_count }}件
                                    @endif
                                </div>
                                @if($shop->concept_type)
                                    <div class="mb-2">
                                        <span class="cc-tag">{{ $shop->concept_type }}</span>
                                    </div>
                                @endif
                                <a href="{{ route('shops.show', $shop->id) }}" class="btn btn-primary btn-sm">
                                    詳細を見る
                                </a>
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
</div>

<!-- 最新情報セクション -->
<div class="cc-container mb-4">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="cc-card">
                <h3 class="h5 mb-4">
                    <i class="fas fa-clock me-2" style="color: var(--cc-color-accent);"></i>最新の求人情報
                </h3>
                @if($latest_jobs->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">まだ求人情報がありません</p>
                    </div>
                @else
                    @foreach($latest_jobs as $job)
                        <div class="cc-job-card mb-3">
                            <div>
                                <img src="{{ $job->shop->image_url ?? asset('assets/images/CafeColle_NoImage.png') }}" 
                                     class="cc-job-thumb" 
                                     alt="{{ $job->shop->name }}"
                                     loading="lazy">
                            </div>
                            <div class="flex-grow-1">
                                <div class="cc-job-title mb-1">
                                    <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none" style="color: inherit;">
                                        {{ $job->title }}
                                    </a>
                                </div>
                                <div class="cc-job-meta mb-2">
                                    {{ $job->shop->name }} ｜ 
                                    {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}
                                    @if($job->salary_min)
                                        ｜ 時給 {{ number_format($job->salary_min) }}円〜
                                        @if($job->salary_max)
                                            {{ number_format($job->salary_max) }}円
                                        @endif
                                    @endif
                                </div>
                                @if($job->shop->concept_type)
                                    <div class="mb-2">
                                        <span class="cc-tag">{{ $job->shop->concept_type }}</span>
                                    </div>
                                @endif
                                <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-primary btn-sm">
                                    詳細を見る
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="cc-card">
                <h3 class="h5 mb-4">
                    <i class="fas fa-store me-2" style="color: var(--cc-color-accent);"></i>最新のお店情報
                </h3>
                @if($latest_shops->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted">まだ店舗情報がありません</p>
                    </div>
                @else
                    @foreach($latest_shops as $shop)
                        <div class="cc-job-card mb-3">
                            <div>
                                <img src="{{ $shop->image_url ?? asset('assets/images/CafeColle_NoImage.png') }}" 
                                     class="cc-job-thumb" 
                                     alt="{{ $shop->name }}"
                                     loading="lazy">
                            </div>
                            <div class="flex-grow-1">
                                <div class="cc-job-title mb-1">
                                    <a href="{{ route('shops.show', $shop->id) }}" class="text-decoration-none" style="color: inherit;">
                                        {{ $shop->name }}
                                    </a>
                                </div>
                                <div class="cc-job-meta mb-2">
                                    {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}
                                    @if($shop->reviews_avg_rating)
                                        ｜ 評価 {{ number_format($shop->reviews_avg_rating, 1) }}
                                    @endif
                                    @if($shop->jobs_count > 0)
                                        ｜ 求人 {{ $shop->jobs_count }}件
                                    @endif
                                </div>
                                @if($shop->concept_type)
                                    <div class="mb-2">
                                        <span class="cc-tag">{{ $shop->concept_type }}</span>
                                    </div>
                                @endif
                                <a href="{{ route('shops.show', $shop->id) }}" class="btn btn-primary btn-sm">
                                    詳細を見る
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- CTA セクション -->
<section class="py-5 mb-4" style="background-color: var(--cc-color-main);">
    <div class="cc-container text-center">
        <h3 class="h4 mb-3">今すぐ始めよう！</h3>
        <p class="mb-4">理想のコンカフェで働く、またはお気に入りのお店を見つけよう</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>無料会員登録
                </a>
            @endguest
            <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-briefcase me-2"></i>求人を探す
            </a>
            <a href="{{ route('shops.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-store me-2"></i>お店を探す
            </a>
        </div>
    </div>
</section>
@endsection
