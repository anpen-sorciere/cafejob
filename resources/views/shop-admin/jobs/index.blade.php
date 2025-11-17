@extends('layouts.shop-admin')

@section('title', '求人管理')

@section('content')
<!-- 店舗管理者ナビゲーション -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('shop-admin.dashboard') }}">
            <i class="fas fa-coffee me-2"></i>カフェコレ（CafeColle）
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="{{ route('shop-admin.jobs.index') }}">
                <i class="fas fa-briefcase me-1"></i>求人管理
            </a>
            <a class="nav-link" href="{{ route('shop-admin.applications.index') }}">
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-briefcase me-2"></i>求人管理
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('shop-admin.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>新規求人作成
                    </a>
                    <a href="{{ route('shop-admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($jobs->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">求人がありません</h5>
                <p class="text-muted">新しい求人を投稿して、優秀なスタッフを見つけましょう。</p>
                <a href="{{ route('shop-admin.jobs.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-1"></i>最初の求人を作成
                </a>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($jobs as $job)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $job->title }}</h6>
                            <span class="badge bg-{{ $job->status === 'active' ? 'success' : ($job->status === 'inactive' ? 'warning' : 'secondary') }}">
                                {{ $job->status === 'active' ? '公開中' : ($job->status === 'inactive' ? '非公開' : '終了') }}
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted small">
                                {{ Str::limit($job->description, 100) }}
                            </p>
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted">応募数</small>
                                    <div class="fw-bold">{{ $job->application_count }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">未読</small>
                                    <div class="fw-bold text-warning">{{ $job->pending_applications }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2">
                                <a href="{{ route('shop-admin.jobs.edit', $job->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>編集
                                </a>
                                <a href="{{ route('shop-admin.applications.index', ['job_id' => $job->id]) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-file-alt me-1"></i>応募
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

