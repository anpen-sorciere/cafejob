@extends('layouts.app')

@section('title', 'キープ一覧')
@section('description', 'キープした求人を一覧で確認し、まとめて応募できます。')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">キープ一覧</h1>
            <p class="text-muted mb-0">気になる求人をキープして、まとめて応募できます。</p>
        </div>
        <div class="text-muted small mt-3 mt-md-0">
            キープ数: {{ $keptJobs->count() }}件
        </div>
    </div>

    @if(session('bulk_results'))
        <div class="mb-4">
            @foreach(session('bulk_results') as $result)
                <div class="alert alert-{{ $result['status'] }} mb-2" role="alert">
                    {{ $result['message'] }}
                </div>
            @endforeach
        </div>
    @endif

    @if($keptJobs->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                <h2 class="h5 mb-3">まだキープした求人はありません</h2>
                <p class="text-muted mb-4">気になる求人をキープして、あとからまとめて応募できます。</p>
                <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>求人を探す
                </a>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('favorites.bulk-apply') }}" class="cj-keep-form mb-5">
            @csrf
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="cj-keep-select-all">
                                <label class="form-check-label fw-semibold" for="cj-keep-select-all">
                                    すべて選択
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="text-muted me-3">選択中: <span id="cj-keep-selected-count">0</span>件</span>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>選択した求人にまとめて応募
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="cj-keep-message" class="form-label">応募メッセージ（任意）</label>
                        <textarea class="form-control" id="cj-keep-message" name="message" rows="3"
                                  placeholder="共通の応募メッセージを入力できます。">{{ old('message') }}</textarea>
                        <div class="form-text">記入したメッセージが選択したすべての求人に送信されます。</div>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach($keptJobs as $favorite)
                    @php
                        $job = $favorite->job;
                        $shop = $job->shop;
                        $location = trim(($shop->prefecture->name ?? '') . ' ' . ($shop->city->name ?? ''));
                        $is_active = $job->status === 'active';
                    @endphp
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 cj-keep-card {{ !$is_active ? 'cj-keep-card-inactive' : '' }}">
                            <img src="{{ $shop->image_url ?? asset('assets/images/CafeColle_NoImage.png') }}"
                                 class="card-img-top" alt="{{ $shop->name }}"
                                 loading="lazy">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input cj-keep-select"
                                               type="checkbox"
                                               id="keep-job-{{ $job->id }}"
                                               name="job_ids[]"
                                               value="{{ $job->id }}"
                                               {{ !$is_active ? 'disabled' : '' }}>
                                        <label class="form-check-label fw-semibold" for="keep-job-{{ $job->id }}">
                                            {{ $job->title }}
                                            @if(!$is_active)
                                                <span class="badge bg-secondary ms-1">募集停止</span>
                                            @endif
                                        </label>
                                    </div>
                                    <span class="badge badge-concept text-uppercase">
                                        {{ $shop->concept_type }}
                                    </span>
                                </div>

                                <p class="text-muted small mb-1">
                                    <i class="fas fa-store me-1"></i>{{ $shop->name }}
                                </p>
                                @if($location)
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $location }}
                                    </p>
                                @endif

                                @if($job->description)
                                    <p class="small text-muted flex-grow-1">
                                        {{ \Illuminate\Support\Str::limit($job->description, 80) }}...
                                    </p>
                                @else
                                    <div class="flex-grow-1"></div>
                                @endif

                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($job->salary_min)
                                            <span class="badge badge-salary">
                                                {{ number_format($job->salary_min) }}円〜
                                                @if($job->salary_max)
                                                    {{ number_format($job->salary_max) }}円
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>詳細
                                    </a>
                                </div>

                                <div class="mt-3">
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm w-100 cj-keep-toggle cj-keep-active"
                                            data-target-type="job"
                                            data-target-id="{{ $job->id }}"
                                            data-kept="1"
                                            data-remove-on-unkeep="1"
                                            data-remove-target=".cj-keep-card"
                                            aria-pressed="true">
                                        <i class="fas fa-heart me-1"></i>
                                        <span class="cj-keep-label">キープ中</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
// すべて選択チェックボックス
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('cj-keep-select-all');
    const selects = document.querySelectorAll('.cj-keep-select:not([disabled])');
    const countSpan = document.getElementById('cj-keep-selected-count');

    function updateCount() {
        const checked = document.querySelectorAll('.cj-keep-select:checked').length;
        countSpan.textContent = checked;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            selects.forEach(select => {
                select.checked = this.checked;
            });
            updateCount();
        });
    }

    selects.forEach(select => {
        select.addEventListener('change', function() {
            updateCount();
            if (selectAll) {
                selectAll.checked = selects.length === document.querySelectorAll('.cj-keep-select:checked').length;
            }
        });
    });

    updateCount();
});
</script>
@endpush
@endsection
