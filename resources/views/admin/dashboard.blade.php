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

    <!-- グラフセクション -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-3">メトリクス分析</h2>
        </div>
        
        <!-- 1. PV/UU/応募数（直近30日折れ線＋棒） -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">PV/UU/応募数（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartPvUuApplications" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 2. 新規求職者登録数（直近30日） -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">新規求職者登録数（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartNewUsers" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 3. 新規掲載店舗数（直近30日） -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">新規掲載店舗数（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartNewShops" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 4. 1店舗あたり平均応募数（折れ線） -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">1店舗あたり平均応募数（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartAverageApplications" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 5. 求人別応募数ランキング（棒） -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">求人別応募数ランキング（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartJobRanking" style="display: none;"></canvas>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const charts = {};
    
    // Chart.jsのデフォルト設定
    Chart.defaults.color = '#666';
    Chart.defaults.font.family = "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.font.size = 12;
    
    // 1. PV/UU/応募数（折れ線＋棒）
    function loadPvUuApplications() {
        const loadingEl = document.querySelector('#chartPvUuApplications').previousElementSibling;
        const canvasEl = document.getElementById('chartPvUuApplications');
        
        fetch('{{ route('admin.api.metrics.pv-uu-applications') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.pvUuApplications = new Chart(canvasEl, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'PV',
                                data: data.pageViews,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                yAxisID: 'y',
                                tension: 0.4
                            },
                            {
                                label: 'UU',
                                data: data.uniqueUsers,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                yAxisID: 'y',
                                tension: 0.4
                            },
                            {
                                label: '応募数',
                                data: data.applications,
                                type: 'bar',
                                backgroundColor: '#f59e0b',
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                            },
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading PV/UU/Applications data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 2. 新規求職者登録数
    function loadNewUsers() {
        const loadingEl = document.querySelector('#chartNewUsers').previousElementSibling;
        const canvasEl = document.getElementById('chartNewUsers');
        
        fetch('{{ route('admin.api.metrics.new-users') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.newUsers = new Chart(canvasEl, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: '新規求職者登録数',
                            data: data.newUsers,
                            backgroundColor: '#8b5cf6',
                            borderColor: '#7c3aed',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading new users data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 3. 新規掲載店舗数
    function loadNewShops() {
        const loadingEl = document.querySelector('#chartNewShops').previousElementSibling;
        const canvasEl = document.getElementById('chartNewShops');
        
        fetch('{{ route('admin.api.metrics.new-shops') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.newShops = new Chart(canvasEl, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: '新規掲載店舗数',
                            data: data.newShops,
                            backgroundColor: '#ec4899',
                            borderColor: '#db2777',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading new shops data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 4. 1店舗あたり平均応募数
    function loadAverageApplications() {
        const loadingEl = document.querySelector('#chartAverageApplications').previousElementSibling;
        const canvasEl = document.getElementById('chartAverageApplications');
        
        fetch('{{ route('admin.api.metrics.average-applications-per-shop') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.averageApplications = new Chart(canvasEl, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: '1店舗あたり平均応募数',
                            data: data.averageApplications,
                            borderColor: '#06b6d4',
                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading average applications data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 5. 求人別応募数ランキング
    function loadJobRanking() {
        const loadingEl = document.querySelector('#chartJobRanking').previousElementSibling;
        const canvasEl = document.getElementById('chartJobRanking');
        
        fetch('{{ route('admin.api.metrics.job-application-ranking') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.jobRanking = new Chart(canvasEl, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: '応募数',
                            data: data.applications,
                            backgroundColor: '#f97316',
                            borderColor: '#ea580c',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading job ranking data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // すべてのグラフを読み込み
    loadPvUuApplications();
    loadNewUsers();
    loadNewShops();
    loadAverageApplications();
    loadJobRanking();
});
</script>
@endpush
@endsection
