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

    <!-- グラフセクション -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-3" style="color: var(--cc-color-text);">メトリクス分析</h2>
        </div>
        
        <!-- 1. 月次応募数（棒） -->
        <div class="col-lg-6 mb-4">
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">月次応募数（直近6ヶ月）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border" style="color: var(--cc-color-accent);" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartMonthlyApplications" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 2. 月次PV（折れ線） -->
        <div class="col-lg-6 mb-4">
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">月次PV（直近6ヶ月）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border" style="color: var(--cc-color-accent);" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartMonthlyPageViews" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 3. 求人別応募数（棒） -->
        <div class="col-lg-6 mb-4">
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">求人別応募数（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border" style="color: var(--cc-color-accent);" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartJobApplications" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 4. 求人別PV（棒） -->
        <div class="col-lg-6 mb-4">
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">求人別PV（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border" style="color: var(--cc-color-accent);" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartJobPageViews" style="display: none;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- 5. 求人別応募率（散布図） -->
        <div class="col-lg-12 mb-4">
            <div class="cc-card">
                <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--cc-color-border); padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: var(--cc-color-text); font-weight: 600;">求人別応募率（直近30日）</h5>
                </div>
                <div class="card-body">
                    <div class="chart-loading text-center py-5">
                        <div class="spinner-border" style="color: var(--cc-color-accent);" role="status">
                            <span class="visually-hidden">読み込み中...</span>
                        </div>
                        <p class="mt-2 text-muted">データを読み込み中...</p>
                    </div>
                    <canvas id="chartJobApplicationRate" style="display: none;"></canvas>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const charts = {};
    
    // Chart.jsのデフォルト設定
    Chart.defaults.color = '#666';
    Chart.defaults.font.family = "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.font.size = 12;
    
    // 1. 月次応募数（棒）
    function loadMonthlyApplications() {
        const loadingEl = document.querySelector('#chartMonthlyApplications').previousElementSibling;
        const canvasEl = document.getElementById('chartMonthlyApplications');
        
        fetch('{{ route('shop-admin.api.metrics.monthly-applications') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.monthlyApplications = new Chart(canvasEl, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: '応募数',
                            data: data.applications,
                            backgroundColor: '#ff4b9e',
                            borderColor: '#ff2f8a',
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
                console.error('Error loading monthly applications data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 2. 月次PV（折れ線）
    function loadMonthlyPageViews() {
        const loadingEl = document.querySelector('#chartMonthlyPageViews').previousElementSibling;
        const canvasEl = document.getElementById('chartMonthlyPageViews');
        
        fetch('{{ route('shop-admin.api.metrics.monthly-page-views') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.monthlyPageViews = new Chart(canvasEl, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'PV',
                            data: data.pageViews,
                            borderColor: '#ff4b9e',
                            backgroundColor: 'rgba(255, 75, 158, 0.1)',
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
                console.error('Error loading monthly page views data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 3. 求人別応募数（棒）
    function loadJobApplications() {
        const loadingEl = document.querySelector('#chartJobApplications').previousElementSibling;
        const canvasEl = document.getElementById('chartJobApplications');
        
        fetch('{{ route('shop-admin.api.metrics.job-applications') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.jobApplications = new Chart(canvasEl, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: '応募数',
                            data: data.applications,
                            backgroundColor: '#ff8fb1',
                            borderColor: '#ff6a9a',
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
                console.error('Error loading job applications data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 4. 求人別PV（棒）
    function loadJobPageViews() {
        const loadingEl = document.querySelector('#chartJobPageViews').previousElementSibling;
        const canvasEl = document.getElementById('chartJobPageViews');
        
        fetch('{{ route('shop-admin.api.metrics.job-page-views') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.jobPageViews = new Chart(canvasEl, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'PV',
                            data: data.pageViews,
                            backgroundColor: '#f8c8d8',
                            borderColor: '#ff8fb1',
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
                console.error('Error loading job page views data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // 5. 求人別応募率（散布図）
    function loadJobApplicationRate() {
        const loadingEl = document.querySelector('#chartJobApplicationRate').previousElementSibling;
        const canvasEl = document.getElementById('chartJobApplicationRate');
        
        fetch('{{ route('shop-admin.api.metrics.job-application-rate') }}')
            .then(response => response.json())
            .then(data => {
                loadingEl.style.display = 'none';
                canvasEl.style.display = 'block';
                
                charts.jobApplicationRate = new Chart(canvasEl, {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: '求人別応募率',
                            data: data.data.map(point => ({
                                x: point.x,
                                y: point.y
                            })),
                            backgroundColor: '#ff4b9e',
                            borderColor: '#ff2f8a',
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return data.data[context[0].dataIndex].label;
                                    },
                                    label: function(context) {
                                        return 'PV: ' + context.parsed.x + ', 応募率: ' + context.parsed.y + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'ページビュー数'
                                },
                                beginAtZero: true
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: '応募率 (%)'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading job application rate data:', error);
                loadingEl.innerHTML = '<p class="text-danger">データの読み込みに失敗しました</p>';
            });
    }
    
    // すべてのグラフを読み込み
    loadMonthlyApplications();
    loadMonthlyPageViews();
    loadJobApplications();
    loadJobPageViews();
    loadJobApplicationRate();
});
</script>
@endpush
@endsection