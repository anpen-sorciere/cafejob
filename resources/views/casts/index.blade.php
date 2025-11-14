@extends('layouts.app')

@section('title', 'キャスト一覧')
@section('description', 'コンカフェのキャスト一覧を表示します。')

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
                    <form method="GET" action="{{ route('casts.index') }}" id="filterForm">
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                   value="{{ request('keyword') }}" 
                                   placeholder="名前、趣味、特技など">
                        </div>
                        
                        <div class="mb-3">
                            <label for="shop" class="form-label">店舗</label>
                            <select class="form-select" id="shop" name="shop">
                                <option value="">すべて</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="age_min" class="form-label">年齢（最小）</label>
                                <input type="number" class="form-control" id="age_min" name="age_min" 
                                       value="{{ request('age_min') }}" min="18" max="50">
                            </div>
                            <div class="col-6">
                                <label for="age_max" class="form-label">年齢（最大）</label>
                                <input type="number" class="form-control" id="age_max" name="age_max" 
                                       value="{{ request('age_max') }}" min="18" max="50">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">血液型</label>
                            <select class="form-select" id="blood_type" name="blood_type">
                                <option value="">すべて</option>
                                <option value="A" {{ request('blood_type') == 'A' ? 'selected' : '' }}>A型</option>
                                <option value="B" {{ request('blood_type') == 'B' ? 'selected' : '' }}>B型</option>
                                <option value="O" {{ request('blood_type') == 'O' ? 'selected' : '' }}>O型</option>
                                <option value="AB" {{ request('blood_type') == 'AB' ? 'selected' : '' }}>AB型</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>検索
                            </button>
                            <a href="{{ route('casts.index') }}" class="btn btn-outline-secondary">
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
                    <h2 class="mb-1">キャスト一覧</h2>
                    <p class="text-muted mb-0">
                        {{ number_format($casts->total()) }}人のキャストが見つかりました
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0">並び順:</label>
                    <select class="form-select" id="sort" style="width: auto;" onchange="sortResults(this.value)">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>新着順</option>
                        <option value="age" {{ request('sort') == 'age' ? 'selected' : '' }}>年齢順</option>
                        <option value="height" {{ request('sort') == 'height' ? 'selected' : '' }}>身長順</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>名前順</option>
                    </select>
                </div>
            </div>
            
            <!-- キャスト一覧 -->
            @if($casts->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">該当するキャストが見つかりませんでした</h4>
                    <p class="text-muted">検索条件を変更して再度お試しください。</p>
                    <a href="{{ route('casts.index') }}" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>条件をリセット
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($casts as $cast)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                @if($cast->profile_image)
                                    <img src="{{ $cast->profile_image }}" 
                                         class="card-img-top" alt="{{ $cast->name }}"
                                         style="height: 250px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 250px;">
                                        <i class="fas fa-user fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('casts.show', $cast->id) }}" 
                                               class="text-decoration-none">
                                                {{ $cast->name }}
                                            </a>
                                        </h5>
                                        <span class="badge bg-primary">
                                            {{ $cast->shop->concept_type }}
                                        </span>
                                    </div>
                                    
                                    @if($cast->nickname)
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $cast->nickname }}
                                        </p>
                                    @endif
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-store me-1"></i>
                                        {{ $cast->shop->name }}
                                    </p>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $cast->shop->prefecture->name ?? '' }}{{ $cast->shop->city->name ?? '' }}
                                    </p>
                                    
                                    <div class="row small mb-2">
                                        @if($cast->age)
                                        <div class="col-6">
                                            <i class="fas fa-birthday-cake me-1"></i>
                                            {{ $cast->age }}歳
                                        </div>
                                        @endif
                                        @if($cast->height)
                                        <div class="col-6">
                                            <i class="fas fa-ruler-vertical me-1"></i>
                                            {{ $cast->height }}cm
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('casts.show', $cast->id) }}" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i>詳細を見る
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- ページネーション -->
                @if($casts->hasPages())
                    <nav aria-label="キャスト一覧のページネーション" class="mt-4">
                        {{ $casts->links() }}
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

