@extends('layouts.app')

@section('title', '求職者報告管理')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>求職者報告管理
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </div>

    <!-- フィルター -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.user-reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">ステータス</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>すべて</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>未確認</option>
                        <option value="reviewed" {{ $statusFilter === 'reviewed' ? 'selected' : '' }}>確認済み</option>
                        <option value="resolved" {{ $statusFilter === 'resolved' ? 'selected' : '' }}>対応済み</option>
                        <option value="dismissed" {{ $statusFilter === 'dismissed' ? 'selected' : '' }}>却下</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="report_type" class="form-label">報告タイプ</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="all" {{ $reportTypeFilter === 'all' ? 'selected' : '' }}>すべて</option>
                        <option value="no_show" {{ $reportTypeFilter === 'no_show' ? 'selected' : '' }}>面接ドタキャン</option>
                        <option value="inappropriate_behavior" {{ $reportTypeFilter === 'inappropriate_behavior' ? 'selected' : '' }}>不適切な行動</option>
                        <option value="false_information" {{ $reportTypeFilter === 'false_information' ? 'selected' : '' }}>虚偽の情報</option>
                        <option value="other" {{ $reportTypeFilter === 'other' ? 'selected' : '' }}>その他</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">検索</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $search }}" placeholder="ユーザー名、メールアドレス、店舗管理者名">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>検索
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 報告一覧 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">報告一覧</h5>
        </div>
        <div class="card-body">
            @if($reports->isEmpty())
                <p class="text-muted text-center py-4">報告がありません</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>報告された求職者</th>
                                <th>報告した店舗管理者</th>
                                <th>報告タイプ</th>
                                <th>報告内容</th>
                                <th>ステータス</th>
                                <th>報告日時</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $report->user->username }}</strong><br>
                                        <small class="text-muted">{{ $report->user->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $report->shopAdmin->username }}</strong><br>
                                        <small class="text-muted">{{ $report->application->job->shop->name ?? '-' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $reportTypeLabels[$report->report_type] ?? $report->report_type }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $report->message }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $report->status === 'pending' ? 'warning' : 
                                        ($report->status === 'reviewed' ? 'info' : 
                                        ($report->status === 'resolved' ? 'success' : 'secondary')) 
                                    }}">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $report->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.user-reports.show', $report->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i>詳細
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

