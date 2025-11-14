@extends('layouts.app')

@section('title', 'ユーザー管理 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>ユーザー管理
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
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">検索</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="ユーザー名、メール、氏名で検索">
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
    
    <!-- ユーザー一覧 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">ユーザー一覧 ({{ number_format($users->total()) }}件)</h5>
                </div>
                <div class="card-body">
                    @if($users->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">ユーザーが見つかりませんでした</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ユーザー名</th>
                                        <th>氏名</th>
                                        <th>メールアドレス</th>
                                        <th>電話番号</th>
                                        <th>応募数</th>
                                        <th>ステータス</th>
                                        <th>登録日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <strong>{{ $user->username }}</strong>
                                            </td>
                                            <td>
                                                {{ $user->last_name }} {{ $user->first_name }}
                                                @if($user->gender)
                                                    <br><small class="text-muted">{{ $user->gender === 'male' ? '男性' : ($user->gender === 'female' ? '女性' : 'その他') }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?: '-' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $user->applications_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ $user->status }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('Y/m/d') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($users->hasPages())
                            <nav aria-label="ページネーション" class="mt-3">
                                {{ $users->links() }}
                            </nav>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

