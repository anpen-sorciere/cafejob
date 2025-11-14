@extends('layouts.app')

@section('title', '求人管理 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-briefcase me-2"></i>求人管理
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
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>募集終了</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">検索</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="求人タイトルまたは店舗名で検索">
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
    
    <!-- 求人一覧 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">求人一覧 ({{ number_format($jobs->total()) }}件)</h5>
                </div>
                <div class="card-body">
                    @if($jobs->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">求人が見つかりませんでした</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>求人タイトル</th>
                                        <th>店舗名</th>
                                        <th>給与</th>
                                        <th>応募数</th>
                                        <th>ステータス</th>
                                        <th>募集締切</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>
                                                <strong>{{ $job->title }}</strong>
                                                <br><small class="text-muted">{{ $job->job_type }}</small>
                                            </td>
                                            <td>{{ $job->shop->name }}</td>
                                            <td>
                                                @if($job->salary_min && $job->salary_max)
                                                    {{ number_format($job->salary_min) }}円 - {{ number_format($job->salary_max) }}円
                                                @elseif($job->salary_min)
                                                    {{ number_format($job->salary_min) }}円以上
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $job->applications_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $job->status === 'active' ? 'success' : 
                                                    ($job->status === 'closed' ? 'danger' : 'secondary') 
                                                }}">
                                                    {{ $job->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($job->application_deadline)
                                                    {{ $job->application_deadline->format('Y/m/d') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.jobs.show', $job->id) }}" class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($jobs->hasPages())
                            <nav aria-label="ページネーション" class="mt-3">
                                {{ $jobs->links() }}
                            </nav>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

