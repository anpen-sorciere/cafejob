@extends('layouts.app')

@section('title', '店舗管理 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-store me-2"></i>店舗管理
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </div>
    
    <!-- フィルター -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">ステータス</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">すべて</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>アクティブ</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>非アクティブ</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>承認待ち</option>
                                <option value="verification_pending" {{ request('status') === 'verification_pending' ? 'selected' : '' }}>確認待ち</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">検索</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="店舗名または住所で検索">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>検索
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 店舗一覧 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">店舗一覧 ({{ number_format($shops->total()) }}件)</h5>
                </div>
                <div class="card-body">
                    @if($shops->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <p class="text-muted">店舗が見つかりませんでした</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>店舗名</th>
                                        <th>住所</th>
                                        <th>電話番号</th>
                                        <th>求人数</th>
                                        <th>ステータス</th>
                                        <th>登録日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shops as $shop)
                                        <tr>
                                            <td>{{ $shop->id }}</td>
                                            <td>
                                                <strong>{{ $shop->name }}</strong>
                                                @if($shop->concept_type)
                                                    <br><small class="text-muted">{{ $shop->concept_type }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}
                                                @if($shop->address)
                                                    <br><small class="text-muted">{{ $shop->address }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $shop->phone ?: '-' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $shop->jobs_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $shop->status === 'active' ? 'success' : 
                                                    ($shop->status === 'pending' || $shop->status === 'verification_pending' ? 'warning' : 'danger') 
                                                }}">
                                                    {{ $shop->status }}
                                                </span>
                                            </td>
                                            <td>{{ $shop->created_at->format('Y/m/d') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.shops.show', $shop->id) }}" class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($shops->hasPages())
                            <nav aria-label="ページネーション" class="mt-3">
                                {{ $shops->links() }}
                            </nav>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

