@extends('layouts.app')

@section('title', $shop->name . ' - 店舗詳細')
@section('description', $shop->description ? \Illuminate\Support\Str::limit($shop->description, 100) : '店舗の詳細情報を表示します。')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- メインコンテンツ -->
        <div class="col-lg-8">
            <!-- パンくずリスト -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shops.index') }}">お店検索</a></li>
                    <li class="breadcrumb-item active">{{ $shop->name }}</li>
                </ol>
            </nav>
            
            <!-- 店舗詳細 -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h1 class="h4 mb-1">{{ $shop->name }}</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}{{ $shop->address }}
                            </p>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <span class="badge bg-primary fs-6">{{ $shop->concept_type }}</span>
                            @if(config('feature_flags.keep', false))
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm cj-keep-toggle {{ $isKept ? 'cj-keep-active' : '' }}"
                                        data-target-type="shop"
                                        data-target-id="{{ $shop->id }}"
                                        data-kept="{{ $isKept ? '1' : '0' }}"
                                        aria-pressed="{{ $isKept ? 'true' : 'false' }}">
                                    <i class="{{ $isKept ? 'fas' : 'far' }} fa-heart me-1"></i>
                                    <span class="cj-keep-label">{{ $isKept ? 'キープ中' : 'キープ' }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($shop->image_url)
                    <img src="{{ $shop->image_url }}" 
                         class="card-img-top" 
                         alt="{{ $shop->name }}" 
                         style="max-height: 400px; object-fit: cover;"
                         loading="lazy">
                @else
                    {{-- 画像がない場合はプレースホルダー背景を表示 --}}
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="min-height: 300px;">
                        <div class="text-center">
                            <i class="fas fa-store fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">画像がありません</p>
                        </div>
                    </div>
                @endif
                
                <div class="card-body">
                    <!-- 店舗説明 -->
                    @if($shop->description)
                    <div class="mb-4">
                        <h5 class="mb-3">店舗について</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($shop->description)) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- お店の雰囲気画像 -->
                    @php
                        $atmosphereImages = is_string($shop->atmosphere_images) ? json_decode($shop->atmosphere_images, true) : ($shop->atmosphere_images ?? []);
                        if (!is_array($atmosphereImages)) {
                            $atmosphereImages = [];
                        }
                    @endphp
                    @if(!empty($atmosphereImages))
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-images me-2 text-primary"></i>お店の雰囲気
                        </h5>
                        <div class="row g-3">
                            @foreach($atmosphereImages as $imageUrl)
                                @if(!empty(trim($imageUrl)))
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card">
                                            <img src="{{ trim($imageUrl) }}" 
                                                 class="card-img-top" 
                                                 alt="お店の雰囲気画像"
                                                 style="height: 200px; object-fit: cover; cursor: pointer;"
                                                 loading="lazy"
                                                 onclick="window.open(this.src, '_blank')"
                                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3E画像が読み込めません%3C/text%3E%3C/svg%3E';">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- アルバイトの特徴 -->
                    @if($shop->job_features)
                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="mb-3">
                            <i class="fas fa-star me-2 text-warning"></i>アルバイトの特徴
                        </h5>
                        <div>
                            {!! nl2br(e($shop->job_features)) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- 基本情報 -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">基本情報</h5>
                            <table class="table table-sm">
                                @if($shop->phone)
                                <tr>
                                    <td><strong>電話番号</strong></td>
                                    <td>{{ $shop->phone }}</td>
                                </tr>
                                @endif
                                @if($shop->email)
                                <tr>
                                    <td><strong>メール</strong></td>
                                    <td>{{ $shop->email }}</td>
                                </tr>
                                @endif
                                @if($shop->website)
                                <tr>
                                    <td><strong>ウェブサイト</strong></td>
                                    <td><a href="{{ $shop->website }}" target="_blank">{{ $shop->website }}</a></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>コンセプト</strong></td>
                                    <td>{{ $shop->concept_type }}</td>
                                </tr>
                                @if($shop->uniform_type)
                                <tr>
                                    <td><strong>制服タイプ</strong></td>
                                    <td>{{ $shop->uniform_type }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">営業時間</h5>
                            @if($shop->opening_hours)
                            <div class="bg-light p-3 rounded">
                                <pre class="mb-0">{{ $shop->opening_hours }}</pre>
                            </div>
                            @else
                            <p class="text-muted">営業時間情報がありません</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- 評価・口コミ -->
                    @if($shop->reviews->isNotEmpty())
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-star me-2 text-warning"></i>評価・口コミ
                            @if($shop->reviews_avg_rating)
                                <span class="badge bg-warning">{{ number_format($shop->reviews_avg_rating, 1) }}</span>
                            @endif
                        </h5>
                        <div class="row">
                            @foreach($shop->reviews->take(3) as $review)
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $review->title ?? '口コミ' }}</strong>
                                                @if($review->rating)
                                                    <div class="mt-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($review->content)
                                            <p class="mb-0">{{ $review->content }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- この店舗の求人 -->
            @if($jobs->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>この店舗の求人
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($jobs as $job)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('jobs.show', $job->id) }}" 
                                           class="text-decoration-none">
                                            {{ $job->title }}
                                        </a>
                                    </h6>
                                    <p class="card-text small text-muted">
                                        {{ $job->job_type }}
                                    </p>
                                    @if($job->salary_min)
                                        <span class="badge bg-primary">
                                            {{ number_format($job->salary_min) }}円〜
                                            @if($job->salary_max)
                                                {{ number_format($job->salary_max) }}円
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- キャスト情報 -->
            @if($shop->casts->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>キャスト情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($shop->casts->take(6) as $cast)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                @if($cast->profile_image)
                                    <img src="{{ $cast->profile_image }}" 
                                         class="card-img-top" alt="{{ $cast->name }}">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ $cast->name }}</h6>
                                    @if($cast->nickname)
                                        <p class="card-text small text-muted">
                                            ニックネーム: {{ $cast->nickname }}
                                        </p>
                                    @endif
                                    @if($cast->age)
                                        <p class="card-text small">年齢: {{ $cast->age }}歳</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- 口コミセクション -->
            <div class="card mb-4" id="reviews">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>口コミ
                        @if($shop->reviews_avg_rating)
                            <span class="badge bg-warning text-dark ms-2">
                                {{ number_format($shop->reviews_avg_rating, 1) }}/5.0
                            </span>
                        @endif
                        <span class="text-muted small ms-2">
                            ({{ $shop->reviews->count() }}件)
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- 口コミ投稿フォーム -->
                    @auth
                        @php
                            $userReview = $shop->reviews->where('user_id', Auth::id())->first();
                        @endphp
                        @if(!$userReview)
                            <div class="mb-4 pb-4 border-bottom">
                                <h6 class="mb-3">口コミを投稿する</h6>
                                <form method="POST" action="{{ route('reviews.store', $shop->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">評価 <span class="text-danger">*</span></label>
                                        <div class="rating-input">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" required>
                                                <label for="rating{{ $i }}" class="star-label">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                        @error('rating')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="review_title" class="form-label">タイトル</label>
                                        <input type="text" class="form-control" id="review_title" name="title" 
                                               value="{{ old('title') }}" maxlength="100">
                                        @error('title')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="review_content" class="form-label">口コミ内容 <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="review_content" name="content" rows="4" 
                                                  required maxlength="1000">{{ old('content') }}</textarea>
                                        <small class="text-muted">最大1000文字</small>
                                        @error('content')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i>口コミを投稿
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-1"></i>この店舗には既に口コミを投稿しています。
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-1"></i>口コミを投稿するには<a href="{{ route('login') }}" class="alert-link">ログイン</a>が必要です。
                        </div>
                    @endauth
                    
                    <!-- 口コミ一覧 -->
                    @if($shop->reviews->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <p class="text-muted">まだ口コミがありません</p>
                        </div>
                    @else
                        @foreach($shop->reviews as $review)
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ms-2 small text-muted">{{ $review->rating }}/5</span>
                                        </div>
                                        @if($review->title)
                                            <h6 class="mb-1">{{ $review->title }}</h6>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        {{ $review->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d') }}
                                    </small>
                                </div>
                                <p class="mb-2">{{ nl2br(e($review->content)) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        @if($review->user)
                                            {{ $review->user->username }}
                                        @else
                                            匿名
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        
        <!-- サイドバー -->
        <div class="col-lg-4">
            <!-- キープ -->
            @if(config('feature_flags.keep', false))
            <div class="card mb-4">
                <div class="card-body text-center">
                    @auth
                        <button type="button"
                                class="btn btn-outline-danger cj-keep-toggle {{ $isKept ? 'cj-keep-active' : '' }}"
                                data-target-type="shop"
                                data-target-id="{{ $shop->id }}"
                                data-kept="{{ $isKept ? '1' : '0' }}"
                                aria-pressed="{{ $isKept ? 'true' : 'false' }}">
                            <i class="{{ $isKept ? 'fas' : 'far' }} fa-heart me-1"></i>
                            <span class="cj-keep-label">{{ $isKept ? 'キープ中' : 'お気に入りに追加' }}</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-danger" onclick="cjRequireLoginModal()">
                            <i class="far fa-heart me-1"></i>お気に入りに追加
                        </button>
                    @endauth
                    <p class="text-muted small mt-2 mb-0">気になる店舗をキープしてマイページから比較できます。</p>
                </div>
            </div>
            @endif
            
            <!-- この店舗の求人（サイドバー） -->
            @if($jobs->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>この店舗の求人
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($jobs->take(3) as $job)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1">
                            <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none">
                                {{ $job->title }}
                            </a>
                        </h6>
                        <p class="small text-muted mb-1">{{ $job->job_type }}</p>
                        @if($job->salary_min)
                            <span class="badge bg-primary small">
                                {{ number_format($job->salary_min) }}円〜
                            </span>
                        @endif
                    </div>
                    @endforeach
                    @if($jobs->count() > 3)
                        <a href="#jobs" class="btn btn-sm btn-outline-primary w-100">
                            すべての求人を見る
                        </a>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- シェア -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-share-alt me-2"></i>シェア
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($shop->name) }}&url={{ urlencode(route('shops.show', $shop->id)) }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('shops.show', $shop->id)) }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('URLをコピーしました');
    });
}
</script>
@endpush
@endsection

