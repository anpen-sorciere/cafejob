@extends('layouts.app')

@section('title', '応募履歴')
@section('description', 'あなたの応募履歴を表示します。')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-file-alt me-2"></i>応募履歴
            </h1>
            
            @if($applications->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">応募履歴がありません</h4>
                    <p class="text-muted">求人に応募すると、ここに履歴が表示されます。</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>求人を探す
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($applications as $application)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('jobs.show', $application->job_id) }}" 
                                               class="text-decoration-none">
                                                {{ $application->job->title }}
                                            </a>
                                        </h5>
                                        <span class="badge bg-{{ 
                                            $application->status == 'pending' ? 'warning' : 
                                            ($application->status == 'accepted' ? 'success' : 'danger') 
                                        }}">
                                            @php
                                                $status_labels = [
                                                    'pending' => '審査中',
                                                    'accepted' => '採用',
                                                    'rejected' => '不採用',
                                                    'cancelled' => 'キャンセル'
                                                ];
                                            @endphp
                                            {{ $status_labels[$application->status] ?? $application->status }}
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-store me-1"></i>
                                        {{ $application->job->shop->name }}
                                    </p>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $application->job->shop->prefecture->name ?? '' }}{{ $application->job->shop->city->name ?? '' }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            @if($application->job->salary_min)
                                                <span class="badge bg-primary">
                                                    {{ number_format($application->job->salary_min) }}円〜
                                                </span>
                                            @endif
                                            <span class="badge bg-secondary">
                                                {{ $application->job->job_type }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $application->applied_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    
                                    @if($application->message)
                                        <div class="mb-3">
                                            <h6 class="small text-muted mb-1">応募メッセージ</h6>
                                            <p class="small bg-light p-2 rounded">
                                                {{ $application->message }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('jobs.show', $application->job_id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>詳細を見る
                                        </a>
                                        <a href="{{ route('chat.show', ['application_id' => $application->id]) }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-comments me-1"></i>チャット
                                        </a>
                                        @if($application->status == 'pending')
                                            <button class="btn btn-outline-danger btn-sm cancel-application" 
                                                    data-application-id="{{ $application->id }}">
                                                <i class="fas fa-times me-1"></i>キャンセル
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// 応募キャンセル機能
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancel-application').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('この応募をキャンセルしますか？')) {
                const applicationId = this.dataset.applicationId;
                
                fetch(`/applications/${applicationId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'エラーが発生しました。');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('エラーが発生しました。');
                });
            }
        });
    });
});
</script>
@endpush
@endsection
