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
                全国のコンカフェの中から、<br>
               「働きたい」「楽しみたい」お店をエリアで探せる<br>
               コンカフェ専門のポータルサイトです。<br>
            </p>
            
            {{-- 統計情報（白カード横並び） - 一時的に非表示 --}}
            {{-- サイト運営が進んで数字が増えてから復活させる --}}
            {{--
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="cc-stat-card">
                        <div class="cc-stat-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9h6a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-2zM2 10a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1H2zm.5-5.5V2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5zm5 0V2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                            </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216Z"/>
                                <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                            </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z"/>
                            </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5z"/>
                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                            </svg>
                        </div>
                        <div class="cc-stat-card-content">
                            <div class="cc-stat-card-number">{{ number_format($stats['total_applications']) }}</div>
                            <div class="cc-stat-card-label">応募数</div>
                        </div>
                    </div>
                </div>
            </div>
            --}}
        
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
