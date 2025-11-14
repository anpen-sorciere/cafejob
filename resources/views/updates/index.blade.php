@extends('layouts.app')

@section('title', '最新情報')
@section('description', 'コンカフェの最新情報をリアルタイムで表示します。')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-clock me-2"></i>最新情報
                <small class="text-muted">リアルタイム更新</small>
            </h1>
        </div>
    </div>
    
    <!-- 統計情報 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $recentJobs->count() }}</h3>
                    <p class="mb-0">30分以内の新着求人</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $recentShops->count() }}</h3>
                    <p class="mb-0">1時間以内の新着店舗</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $recentApplications->count() }}</h3>
                    <p class="mb-0">1時間以内の応募</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $recentCasts->count() }}</h3>
                    <p class="mb-0">2時間以内の新着キャスト</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- 最新求人情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>最新求人情報
                        <span class="badge bg-light text-primary ms-2">{{ $recentJobs->count() }}件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($recentJobs->isEmpty())
                        <p class="text-muted text-center">30分以内の新着求人はありません</p>
                    @else
                        @foreach($recentJobs as $job)
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('jobs.show', $job->id) }}" 
                                           class="text-decoration-none">
                                            {{ $job->title }}
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        {{ $job->shop->name }} | 
                                        {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $job->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">{{ $job->shop->concept_type }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        
        <!-- 最新店舗情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>最新店舗情報
                        <span class="badge bg-light text-success ms-2">{{ $recentShops->count() }}件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($recentShops->isEmpty())
                        <p class="text-muted text-center">1時間以内の新着店舗はありません</p>
                    @else
                        @foreach($recentShops as $shop)
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('shops.show', $shop->id) }}" 
                                           class="text-decoration-none">
                                            {{ $shop->name }}
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        {{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $shop->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success">{{ $shop->concept_type }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- 最新応募情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>最新応募情報
                        <span class="badge bg-light text-info ms-2">{{ $recentApplications->count() }}件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($recentApplications->isEmpty())
                        <p class="text-muted text-center">1時間以内の応募はありません</p>
                    @else
                        @foreach($recentApplications as $application)
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('jobs.show', $application->job_id) }}" 
                                           class="text-decoration-none">
                                            {{ $application->job->title }}
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        {{ $application->job->shop->name }} | 
                                        {{ $application->user->username }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $application->applied_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-info">応募</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        
        <!-- 最新キャスト情報 -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>最新キャスト情報
                        <span class="badge bg-light text-warning ms-2">{{ $recentCasts->count() }}件</span>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($recentCasts->isEmpty())
                        <p class="text-muted text-center">2時間以内の新着キャストはありません</p>
                    @else
                        @foreach($recentCasts as $cast)
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('casts.show', $cast->id) }}" 
                                           class="text-decoration-none">
                                            {{ $cast->name }}
                                        </a>
                                    </h6>
                                    <p class="mb-1 small text-muted">
                                        {{ $cast->shop->name }}
                                        @if($cast->age)
                                            | {{ $cast->age }}歳
                                        @endif
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $cast->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-warning text-dark">{{ $cast->shop->concept_type }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- 自動更新情報 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted mb-0">
                        <i class="fas fa-sync-alt me-2"></i>
                        このページは自動的に更新されます（30秒ごと）
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// 30秒ごとに自動更新
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush
@endsection

