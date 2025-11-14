@extends('layouts.app')

@section('title', $cast->name . ' - キャスト詳細')
@section('description', $cast->nickname ? $cast->nickname . 'の詳細情報を表示します。' : 'キャストの詳細情報を表示します。')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- メインコンテンツ -->
        <div class="col-lg-8">
            <!-- パンくずリスト -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('casts.index') }}">キャスト一覧</a></li>
                    <li class="breadcrumb-item active">{{ $cast->name }}</li>
                </ol>
            </nav>
            
            <!-- キャスト詳細 -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($cast->profile_image)
                                <img src="{{ $cast->profile_image }}" 
                                     class="img-fluid rounded" alt="{{ $cast->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 300px;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h3 mb-1">{{ $cast->name }}</h1>
                                    @if($cast->nickname)
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $cast->nickname }}
                                        </p>
                                    @endif
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-store me-1"></i>
                                        {{ $cast->shop->name }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary fs-6">{{ $cast->shop->concept_type }}</span>
                                </div>
                            </div>
                            
                            <!-- 基本情報 -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3">基本情報</h5>
                                    <table class="table table-sm">
                                        @if($cast->age)
                                        <tr>
                                            <td><strong>年齢</strong></td>
                                            <td>{{ $cast->age }}歳</td>
                                        </tr>
                                        @endif
                                        @if($cast->height)
                                        <tr>
                                            <td><strong>身長</strong></td>
                                            <td>{{ $cast->height }}cm</td>
                                        </tr>
                                        @endif
                                        @if($cast->blood_type)
                                        <tr>
                                            <td><strong>血液型</strong></td>
                                            <td>{{ $cast->blood_type }}型</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>所属店舗</strong></td>
                                            <td>{{ $cast->shop->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>登録日</strong></td>
                                            <td>{{ $cast->created_at->format('Y年m月d日') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">プロフィール</h5>
                                    @if($cast->hobby)
                                    <div class="mb-3">
                                        <h6 class="small text-muted mb-1">趣味</h6>
                                        <p class="mb-0">{{ $cast->hobby }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($cast->special_skill)
                                    <div class="mb-3">
                                        <h6 class="small text-muted mb-1">特技</h6>
                                        <p class="mb-0">{{ $cast->special_skill }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 店舗情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>所属店舗情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>{{ $cast->shop->name }}</h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $cast->shop->prefecture->name ?? '' }}{{ $cast->shop->city->name ?? '' }}{{ $cast->shop->address }}
                            </p>
                            @if($cast->shop->phone)
                            <p class="text-muted mb-2">
                                <i class="fas fa-phone me-1"></i>
                                {{ $cast->shop->phone }}
                            </p>
                            @endif
                            @if($cast->shop->website)
                            <p class="text-muted mb-2">
                                <i class="fas fa-globe me-1"></i>
                                <a href="{{ $cast->shop->website }}" target="_blank">
                                    {{ $cast->shop->website }}
                                </a>
                            </p>
                            @endif
                            @if($cast->shop->opening_hours)
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock me-1"></i>
                                <pre class="mb-0 small">{{ $cast->shop->opening_hours }}</pre>
                            </p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if($cast->shop->image_url)
                                <img src="{{ $cast->shop->image_url }}" 
                                     class="img-fluid rounded" alt="{{ $cast->shop->name }}">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 同じ店舗の他のキャスト -->
            @if($relatedCasts->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>同じ店舗の他のキャスト
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedCasts as $relatedCast)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                @if($relatedCast->profile_image)
                                    <img src="{{ $relatedCast->profile_image }}" 
                                         class="card-img-top" alt="{{ $relatedCast->name }}">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 150px;">
                                        <i class="fas fa-user fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('casts.show', $relatedCast->id) }}" 
                                           class="text-decoration-none">
                                            {{ $relatedCast->name }}
                                        </a>
                                    </h6>
                                    @if($relatedCast->nickname)
                                        <p class="card-text small text-muted">
                                            {{ $relatedCast->nickname }}
                                        </p>
                                    @endif
                                    @if($relatedCast->age)
                                        <p class="card-text small">{{ $relatedCast->age }}歳</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- 店舗の求人情報 -->
            @if($shopJobs->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>店舗の求人情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($shopJobs as $job)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('jobs.show', $job->id) }}" 
                                           class="text-decoration-none">
                                            {{ $job->title }}
                                        </a>
                                    </h6>
                                    <p class="card-text small text-muted">
                                        {{ $cast->shop->name }}
                                    </p>
                                    @if($job->salary_min)
                                        <span class="badge bg-primary">
                                            {{ number_format($job->salary_min) }}円〜
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- サイドバー -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <a href="{{ route('shops.show', $cast->shop_id) }}" class="btn btn-outline-primary btn-sm">
                        店舗詳細を見る
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

