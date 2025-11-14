@extends('layouts.app')

@section('title', '口コミ管理')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-star me-2"></i>口コミ管理
            </h1>
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
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>承認待ち</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>承認済み</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>却下</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">検索</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="店舗名、口コミ内容、ユーザー名で検索">
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
    
    <!-- 口コミ一覧 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">口コミ一覧 ({{ number_format($reviews->total()) }}件)</h5>
                </div>
                <div class="card-body">
                    @if($reviews->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <p class="text-muted">口コミが見つかりませんでした</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>店舗</th>
                                        <th>評価</th>
                                        <th>口コミ内容</th>
                                        <th>投稿者</th>
                                        <th>ステータス</th>
                                        <th>投稿日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>{{ $review->id }}</td>
                                            <td>
                                                <strong>{{ $review->shop->name }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                    <span class="ms-2">{{ $review->rating }}/5</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">
                                                    @if($review->title)
                                                        <strong>{{ $review->title }}</strong><br>
                                                    @endif
                                                    {{ \Illuminate\Support\Str::limit($review->content, 100) }}
                                                    @if(strlen($review->content) > 100)
                                                        <span class="text-muted">...</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($review->user)
                                                    <strong>{{ $review->user->username }}</strong>
                                                    @if($review->user->first_name || $review->user->last_name)
                                                        <br><small class="text-muted">{{ $review->user->last_name }} {{ $review->user->first_name }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">匿名</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning') }}">
                                                    @if($review->status === 'approved')
                                                        承認済み
                                                    @elseif($review->status === 'rejected')
                                                        却下
                                                    @else
                                                        承認待ち
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $review->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($review->status !== 'approved')
                                                        <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success" 
                                                                    onclick="return confirm('この口コミを承認しますか？')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($review->status !== 'rejected')
                                                        <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger" 
                                                                    onclick="return confirm('この口コミを却下しますか？')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- ページネーション -->
                        <div class="mt-4">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

