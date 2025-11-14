@extends('layouts.app')

@section('title', '応募管理 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-file-alt me-2"></i>応募管理
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
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>審査中</option>
                                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>採用</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>不採用</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">検索</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="求人タイトル、店舗名、ユーザー名で検索">
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
    
    <!-- 応募一覧 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">応募一覧 ({{ number_format($applications->total()) }}件)</h5>
                </div>
                <div class="card-body">
                    @if($applications->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">応募が見つかりませんでした</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>求人</th>
                                        <th>店舗</th>
                                        <th>応募者</th>
                                        <th>ステータス</th>
                                        <th>応募日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>{{ $application->id }}</td>
                                            <td>
                                                <strong>{{ $application->job->title }}</strong>
                                            </td>
                                            <td>{{ $application->job->shop->name }}</td>
                                            <td>
                                                <strong>{{ $application->user->username }}</strong>
                                                <br><small class="text-muted">{{ $application->user->last_name }} {{ $application->user->first_name }}</small>
                                                <br><small class="text-muted">{{ $application->user->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $application->status === 'accepted' ? 'success' : 
                                                    ($application->status === 'rejected' ? 'danger' : 
                                                    ($application->status === 'cancelled' ? 'secondary' : 'warning')) 
                                                }}">
                                                    @php
                                                        $statusLabels = [
                                                            'pending' => '審査中',
                                                            'accepted' => '採用',
                                                            'rejected' => '不採用',
                                                            'cancelled' => 'キャンセル'
                                                        ];
                                                    @endphp
                                                    {{ $statusLabels[$application->status] ?? $application->status }}
                                                </span>
                                            </td>
                                            <td>{{ $application->applied_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.applications.show', $application->id) }}" class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($applications->hasPages())
                            <nav aria-label="ページネーション" class="mt-3">
                                {{ $applications->links() }}
                            </nav>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

