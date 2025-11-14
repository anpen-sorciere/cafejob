@extends('layouts.shop-admin')

@section('title', '応募管理')

@section('content')
<!-- 店舗管理者ナビゲーション -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('shop-admin.dashboard') }}">
            <i class="fas fa-coffee me-2"></i>カフェJob
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="{{ route('shop-admin.applications.index') }}">
                <i class="fas fa-file-alt me-1"></i>応募管理
            </a>
            <a class="nav-link" href="{{ route('shop-admin.chat.index') }}">
                <i class="fas fa-comments me-1"></i>チャット
            </a>
            <a class="nav-link" href="{{ route('shop-admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-1"></i>ログアウト
            </a>
            <form id="logout-form" action="{{ route('shop-admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-file-alt me-2"></i>応募管理
            </h1>
        </div>
    </div>

    <!-- フィルター -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('shop-admin.applications.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">ステータス</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>すべて</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>審査中</option>
                            <option value="accepted" {{ $statusFilter === 'accepted' ? 'selected' : '' }}>採用</option>
                            <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>不採用</option>
                            <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">求人</label>
                        <select name="job_id" class="form-select">
                            <option value="all" {{ $jobFilter === 'all' ? 'selected' : '' }}>すべて</option>
                            @foreach($shopJobs as $job)
                                <option value="{{ $job->id }}" {{ $jobFilter == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">検索</label>
                        <input type="text" name="search" class="form-control" placeholder="応募者名、メール、求人タイトル" value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>検索
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 応募一覧 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">応募一覧</h5>
        </div>
        <div class="card-body">
            @if($applications->isEmpty())
                <p class="text-muted text-center py-4">応募情報がありません</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>求人</th>
                                <th>応募者</th>
                                <th>ステータス</th>
                                <th>応募日時</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->job->title }}</td>
                                    <td>
                                        {{ $application->user->last_name }} {{ $application->user->first_name }}
                                        <small class="text-muted d-block">{{ $application->user->username }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $application->status === 'accepted' ? 'success' : 
                                            ($application->status === 'pending' ? 'warning' : 
                                            ($application->status === 'rejected' ? 'danger' : 'secondary')) 
                                        }}">
                                            {{ $application->status === 'accepted' ? '採用' : 
                                               ($application->status === 'pending' ? '審査中' : 
                                               ($application->status === 'rejected' ? '不採用' : 'キャンセル')) }}
                                        </span>
                                    </td>
                                    <td>{{ $application->applied_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('shop-admin.applications.show', $application->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>詳細
                                        </a>
                                        <a href="{{ route('shop-admin.chat.show', ['application_id' => $application->id]) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-comments me-1"></i>チャット
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

