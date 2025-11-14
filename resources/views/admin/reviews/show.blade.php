@extends('layouts.app')

@section('title', '口コミ詳細')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-star me-2"></i>口コミ詳細
                </h1>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>一覧に戻る
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">口コミ情報</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">評価</label>
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 1.5rem;"></i>
                            @endfor
                            <span class="ms-2 fs-5">{{ $review->rating }}/5</span>
                        </div>
                    </div>
                    
                    @if($review->title)
                    <div class="mb-3">
                        <label class="form-label fw-bold">タイトル</label>
                        <p class="mb-0">{{ $review->title }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">口コミ内容</label>
                        <div class="border rounded p-3 bg-light">
                            {{ nl2br(e($review->content)) }}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ステータス</label>
                            <div>
                                <span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning') }} fs-6">
                                    @if($review->status === 'approved')
                                        承認済み
                                    @elseif($review->status === 'rejected')
                                        却下
                                    @else
                                        承認待ち
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">投稿日時</label>
                            <p class="mb-0">{{ $review->created_at->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">店舗情報</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ $review->shop->name }}</strong>
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $review->shop->prefecture->name ?? '' }}{{ $review->shop->city->name ?? '' }}
                    </p>
                    <a href="{{ route('admin.shops.show', $review->shop->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-eye me-1"></i>店舗詳細を見る
                    </a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">投稿者情報</h5>
                </div>
                <div class="card-body">
                    @if($review->user)
                        <p class="mb-2">
                            <strong>{{ $review->user->username }}</strong>
                        </p>
                        @if($review->user->first_name || $review->user->last_name)
                            <p class="text-muted small mb-2">
                                {{ $review->user->last_name }} {{ $review->user->first_name }}
                            </p>
                        @endif
                        <p class="text-muted small mb-0">
                            <i class="fas fa-envelope me-1"></i>{{ $review->user->email }}
                        </p>
                        <a href="{{ route('admin.users.show', $review->user->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-eye me-1"></i>ユーザー詳細を見る
                        </a>
                    @else
                        <p class="text-muted mb-0">匿名</p>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">操作</h5>
                </div>
                <div class="card-body">
                    @if($review->status !== 'approved')
                        <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('この口コミを承認しますか？')">
                                <i class="fas fa-check me-1"></i>承認
                            </button>
                        </form>
                    @endif
                    @if($review->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('この口コミを却下しますか？')">
                                <i class="fas fa-times me-1"></i>却下
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

