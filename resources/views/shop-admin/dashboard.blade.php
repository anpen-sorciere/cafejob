@extends('layouts.shop-admin')

@section('title', ($shop ? $shop->name : '店舗') . '店舗管理者ダッシュボード')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-store me-2"></i>{{ $shop ? $shop->name : '店舗' }}店舗管理者ダッシュボード
            </h1>
        </div>
    </div>

    <!-- 統計カード -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ number_format($stats['total_jobs']) }}</h3>
                    <p class="mb-0">総求人数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ number_format($stats['active_jobs']) }}</h3>
                    <p class="mb-0">公開中求人</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ number_format($stats['total_applications']) }}</h3>
                    <p class="mb-0">総応募数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ number_format($stats['pending_applications']) }}</h3>
                    <p class="mb-0">審査中応募</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 最新の応募 -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">最新の応募</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($recentApplications->isEmpty())
                        <p class="text-muted">応募情報がありません</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>求人</th>
                                        <th>応募者</th>
                                        <th>ステータス</th>
                                        <th>応募日時</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $application)
                                        <tr>
                                            <td>{{ $application->job->title }}</td>
                                            <td>{{ $application->user->username }}</td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $application->status == 'pending' ? 'warning' : 
                                                    ($application->status == 'accepted' ? 'success' : 'danger') 
                                                }}">
                                                    {{ $application->status == 'pending' ? '審査中' : 
                                                       ($application->status == 'accepted' ? '採用' : '不採用') }}
                                                </span>
                                            </td>
                                            <td>{{ $application->applied_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">自店舗の求人</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($shopJobs->isEmpty())
                        <p class="text-muted">求人情報がありません</p>
                    @else
                        @foreach($shopJobs as $job)
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="mb-1">{{ $job->title }}</h6>
                                <small class="text-muted d-block">{{ $job->status == 'active' ? '公開中' : '非公開' }}</small>
                                <small class="text-muted">{{ $job->created_at->format('Y/m/d') }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection