@extends('layouts.shop-admin')

@section('title', ($shop ? $shop->name : '店舗') . '店舗管理者ダッシュボード')

@section('content')
<div class="cc-container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-4" style="color: var(--cc-color-text);">
                <i class="fas fa-store me-2" style="color: var(--cc-color-accent);"></i>{{ $shop ? $shop->name : '店舗' }}店舗管理者ダッシュボード
            </h1>
        </div>
    </div>

    <!-- 統計カード -->
    <div class="row mb-4 g-3">
        <div class="col-md-3 col-6">
            <div class="cc-card text-center">
                <div class="card-body">
                    <h3 class="mb-2" style="color: var(--cc-color-accent); font-size: 2rem; font-weight: 700;">{{ number_format($stats['total_jobs']) }}</h3>
                    <p class="mb-0" style="color: var(--cc-color-muted); font-size: 0.9rem;">総求人数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="cc-card text-center">
                <div class="card-body">
                    <h3 class="mb-2" style="color: #198754; font-size: 2rem; font-weight: 700;">{{ number_format($stats['active_jobs']) }}</h3>
                    <p class="mb-0" style="color: var(--cc-color-muted); font-size: 0.9rem;">公開中求人</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="cc-card text-center">
                <div class="card-body">
                    <h3 class="mb-2" style="color: var(--cc-color-accent); font-size: 2rem; font-weight: 700;">{{ number_format($stats['total_applications']) }}</h3>
                    <p class="mb-0" style="color: var(--cc-color-muted); font-size: 0.9rem;">総応募数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="cc-card text-center">
                <div class="card-body">
                    <h3 class="mb-2" style="color: #ffc107; font-size: 2rem; font-weight: 700;">{{ number_format($stats['pending_applications']) }}</h3>
                    <p class="mb-0" style="color: var(--cc-color-muted); font-size: 0.9rem;">審査中応募</p>
                </div>
            </div>
        </div>
    </div>

    <!-- クイックアクション -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="cc-card">
                <div class="card-body">
                    <h5 class="mb-3" style="color: var(--cc-color-text); font-weight: 600;">
                        <i class="fas fa-bolt me-2" style="color: var(--cc-color-accent);"></i>クイックアクション
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('shop-admin.shop-info') }}" class="btn btn-primary w-100" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px;">
                                <i class="fas fa-store"></i>
                                <span>店舗情報を編集</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('shop-admin.jobs.create') }}" class="btn btn-primary w-100" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px;">
                                <i class="fas fa-plus-circle"></i>
                                <span>新しい求人を投稿</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('shop-admin.jobs.index') }}" class="btn btn-outline-primary w-100" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px;">
                                <i class="fas fa-briefcase"></i>
                                <span>求人を管理</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 最新の応募 -->
    <div class="row mb-4 g-4">
        <div class="col-md-8">
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">
                        <i class="fas fa-file-alt me-2" style="color: var(--cc-color-accent);"></i>最新の応募
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto; padding: 1.5rem;">
                    @if($recentApplications->isEmpty())
                        <p class="text-muted mb-0">応募情報がありません</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="color: var(--cc-color-text); font-weight: 600;">求人</th>
                                        <th style="color: var(--cc-color-text); font-weight: 600;">応募者</th>
                                        <th style="color: var(--cc-color-text); font-weight: 600;">ステータス</th>
                                        <th style="color: var(--cc-color-text); font-weight: 600;">応募日時</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $application)
                                        <tr>
                                            <td>{{ $application->job->title }}</td>
                                            <td>{{ $application->user->username }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ 
                                                    $application->status == 'pending' ? '#ffc107' : 
                                                    ($application->status == 'accepted' ? '#198754' : '#dc3545') 
                                                }}; color: #fff;">
                                                    {{ $application->status == 'pending' ? '審査中' : 
                                                       ($application->status == 'accepted' ? '採用' : '不採用') }}
                                                </span>
                                            </td>
                                            <td style="color: var(--cc-color-muted);">{{ $application->applied_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
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
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">
                        <i class="fas fa-briefcase me-2" style="color: var(--cc-color-accent);"></i>自店舗の求人
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto; padding: 1.5rem;">
                    @if($shopJobs->isEmpty())
                        <p class="text-muted mb-0">求人情報がありません</p>
                    @else
                        @foreach($shopJobs as $job)
                            <div class="mb-3 pb-3" style="border-bottom: 1px solid var(--cc-color-border);">
                                <h6 class="mb-1" style="color: var(--cc-color-text); font-weight: 600;">{{ $job->title }}</h6>
                                <small class="d-block mb-1" style="color: var(--cc-color-muted);">
                                    <span class="badge" style="background-color: {{ $job->status == 'active' ? '#198754' : '#6c757d' }}; color: #fff; font-size: 0.75rem;">
                                        {{ $job->status == 'active' ? '公開中' : '非公開' }}
                                    </span>
                                </small>
                                <small style="color: var(--cc-color-muted);">{{ $job->created_at->format('Y/m/d') }}</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection