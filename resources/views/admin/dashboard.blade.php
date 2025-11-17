@extends('layouts.app')

@section('title', 'カフェコレ（CafeColle）システム管理者ダッシュボード')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-shield-alt me-2"></i>カフェコレ（CafeColle）システム管理者ダッシュボード
            </h1>
        </div>
    </div>

    <!-- 統計カード -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ number_format($stats['total_users']) }}</h3>
                    <p class="mb-0">総ユーザー数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ number_format($stats['total_shops']) }}</h3>
                    <p class="mb-0">総店舗数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ number_format($stats['total_jobs']) }}</h3>
                    <p class="mb-0">総求人数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ number_format($stats['total_applications']) }}</h3>
                    <p class="mb-0">総応募数</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-danger">{{ number_format($stats['pending_shops']) }}</h3>
                    <p class="mb-0">承認待ち店舗</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-secondary">{{ number_format($stats['total_casts']) }}</h3>
                    <p class="mb-0">総キャスト数</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-dark">{{ number_format($stats['pending_reviews']) }}</h3>
                    <p class="mb-0">承認待ち口コミ</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h3 class="text-danger">{{ number_format($stats['pending_reports']) }}</h3>
                    <p class="mb-0">未確認の求職者報告</p>
                </div>
            </div>
        </div>
    </div>

    <!-- クイックアクション -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">クイックアクション</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.shops.index') }}" class="btn btn-primary">
                            <i class="fas fa-store me-1"></i>店舗管理
                        </a>
                        <a href="{{ route('admin.jobs.index') }}" class="btn btn-info">
                            <i class="fas fa-briefcase me-1"></i>求人管理
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-success">
                            <i class="fas fa-users me-1"></i>ユーザー管理
                        </a>
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-warning">
                            <i class="fas fa-file-alt me-1"></i>応募管理
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary">
                            <i class="fas fa-star me-1"></i>口コミ管理
                        </a>
                        <a href="{{ route('admin.user-reports.index') }}" class="btn btn-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>求職者報告管理
                            @if($stats['pending_reports'] > 0)
                                <span class="badge bg-light text-dark ms-1">{{ $stats['pending_reports'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-1"></i>サイトトップ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 最新情報 -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">最新の応募</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if($recentApplications->isEmpty())
                        <p class="text-muted small">応募情報がありません</p>
                    @else
                        @foreach($recentApplications as $application)
                            <div class="mb-2 pb-2 border-bottom">
                                <small class="d-block">{{ $application->job->title }}</small>
                                <small class="text-muted">{{ $application->user->username }} | {{ $application->applied_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">最新の店舗</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if($recentShops->isEmpty())
                        <p class="text-muted small">店舗情報がありません</p>
                    @else
                        @foreach($recentShops as $shop)
                            <div class="mb-2 pb-2 border-bottom">
                                <small class="d-block">{{ $shop->name }}</small>
                                <small class="text-muted">{{ $shop->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">最新の口コミ</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if($recentReviews->isEmpty())
                        <p class="text-muted small">口コミ情報がありません</p>
                    @else
                        @foreach($recentReviews as $review)
                            <div class="mb-2 pb-2 border-bottom">
                                <small class="d-block">{{ $review->shop->name }}</small>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
