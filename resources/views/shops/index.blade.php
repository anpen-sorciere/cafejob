@extends('layouts.app')

@section('title', 'お店検索')
@section('description', 'コンカフェのお店を検索できます。エリア、コンセプト、制服などで絞り込み検索が可能です。')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- サイドバー（検索フィルター） -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>検索フィルター
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('shops.index') }}" id="filterForm">
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                   value="{{ request('keyword') }}" 
                                   placeholder="店舗名、コンセプトなど">
                        </div>
                        
                        <div class="mb-3">
                            <label for="prefecture" class="form-label">都道府県</label>
                            <select class="form-select" id="prefecture" name="prefecture">
                                <option value="">すべて</option>
                                @foreach($prefectures as $prefecture)
                                    <option value="{{ $prefecture->id }}" {{ request('prefecture') == $prefecture->id ? 'selected' : '' }}>
                                        {{ $prefecture->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="concept_type" class="form-label">コンセプト</label>
                            <select class="form-select" id="concept_type" name="concept_type">
                                <option value="">すべて</option>
                                <option value="maid" {{ request('concept_type') == 'maid' ? 'selected' : '' }}>メイドカフェ</option>
                                <option value="butler" {{ request('concept_type') == 'butler' ? 'selected' : '' }}>執事喫茶</option>
                                <option value="idol" {{ request('concept_type') == 'idol' ? 'selected' : '' }}>アイドルカフェ</option>
                                <option value="cosplay" {{ request('concept_type') == 'cosplay' ? 'selected' : '' }}>コスプレカフェ</option>
                                <option value="other" {{ request('concept_type') == 'other' ? 'selected' : '' }}>その他</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="uniform_type" class="form-label">制服タイプ</label>
                            <select class="form-select" id="uniform_type" name="uniform_type">
                                <option value="">すべて</option>
                                <option value="メイド" {{ request('uniform_type') == 'メイド' ? 'selected' : '' }}>メイド</option>
                                <option value="執事" {{ request('uniform_type') == '執事' ? 'selected' : '' }}>執事</option>
                                <option value="アイドル" {{ request('uniform_type') == 'アイドル' ? 'selected' : '' }}>アイドル</option>
                                <option value="コスプレ" {{ request('uniform_type') == 'コスプレ' ? 'selected' : '' }}>コスプレ</option>
                                <option value="制服" {{ request('uniform_type') == '制服' ? 'selected' : '' }}>制服</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>検索
                            </button>
                            <a href="{{ route('shops.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>リセット
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- メインコンテンツ -->
        <div class="col-lg-9">
            <!-- 検索結果ヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">お店検索結果</h2>
                    <p class="text-muted mb-0">
                        {{ number_format($shops->total()) }}件のお店が見つかりました
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0">並び順:</label>
                    <select class="form-select" id="sort" style="width: auto;" onchange="sortResults(this.value)">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>新着順</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>評価順</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>人気順</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>名前順</option>
                    </select>
                </div>
            </div>
            
            <!-- お店一覧 -->
            @if($shops->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">該当するお店が見つかりませんでした</h4>
                    <p class="text-muted">検索条件を変更して再度お試しください。</p>
                    <a href="{{ route('shops.index') }}" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>条件をリセット
                    </a>
                </div>
            @else
                <div>
                    @foreach($shops as $shop)
                        <div class="cc-job-card">
                            <div>
                                @if($shop->image_url)
                                    <img src="{{ $shop->image_url }}" 
                                         class="cc-job-thumb" 
                                         alt="{{ $shop->name }}"
                                         loading="lazy">
                                @else
                                    <div class="cc-job-thumb d-flex align-items-center justify-content-center">
                                        <i class="fas fa-store fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="cc-job-title">
                                    <a href="{{ route('shops.show', $shop->id) }}" class="text-decoration-none" style="color: inherit;">
                                        {{ $shop->name }}
                                    </a>
                                </div>
                                <div class="cc-job-meta mb-2">
                                    {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}
                                    @if($shop->reviews_avg_rating)
                                        / 評価 {{ number_format($shop->reviews_avg_rating, 1) }}
                                    @endif
                                    @if($shop->jobs_count > 0)
                                        / 求人 {{ $shop->jobs_count }}件
                                    @endif
                                </div>
                                <div class="mb-2">
                                    @if($shop->concept_type)
                                        <span class="cc-tag">{{ $shop->concept_type }}</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <a href="{{ route('shops.show', $shop->id) }}" class="btn btn-primary btn-sm">
                                        詳細を見る
                                    </a>
                                    @if(config('feature_flags.keep', false))
                                        @auth
                                            @php
                                                $shop_is_kept = in_array($shop->id, $keptShopIds ?? [], true);
                                            @endphp
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm cj-keep-toggle {{ $shop_is_kept ? 'cj-keep-active' : '' }}"
                                                    data-target-type="shop"
                                                    data-target-id="{{ $shop->id }}"
                                                    data-kept="{{ $shop_is_kept ? '1' : '0' }}"
                                                    aria-pressed="{{ $shop_is_kept ? 'true' : 'false' }}">
                                                <i class="{{ $shop_is_kept ? 'fas' : 'far' }} fa-heart me-1"></i>
                                                <span class="cj-keep-label">{{ $shop_is_kept ? 'キープ中' : 'キープ' }}</span>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="cjRequireLoginModal()">
                                                <i class="far fa-heart me-1"></i>キープ
                                            </button>
                                        @endauth
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- ページネーション -->
                @if($shops->hasPages())
                    <nav aria-label="お店検索結果のページネーション" class="mt-4">
                        {{ $shops->links() }}
                    </nav>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function sortResults(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}
</script>
@endpush
@endsection

