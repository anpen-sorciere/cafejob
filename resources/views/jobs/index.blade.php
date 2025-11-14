@extends('layouts.app')

@section('title', '求人検索')
@section('description', 'コンカフェ専門の求人検索。エリア、給与、コンセプトなどで絞り込み検索できます。')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- サイドバー（検索フィルター） -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>検索フィルター
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.index') }}" id="filterForm">
                        <div class="mb-3">
                            <label for="keyword" class="form-label">キーワード</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                   value="{{ request('keyword') }}" 
                                   placeholder="店舗名、職種など">
                        </div>
                        
                        <div class="mb-3">
                            <label for="prefecture" class="form-label">都道府県</label>
                            <select class="form-select" id="prefecture" name="prefecture">
                                <option value="">すべて</option>
                                @foreach($prefectures as $prefecture)
                                    <option value="{{ $prefecture->id }}" {{ request('prefecture') == $prefecture->id ? 'selected' : '' }}>
                                        {{ $prefecture->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="concept_type" class="form-label">コンセプト</label>
                            <select class="form-select" id="concept_type" name="concept_type">
                                <option value="">すべて</option>
                                <option value="maid" {{ request('concept_type') == 'maid' ? 'selected' : '' }}>メイドカフェ</option>
                                <option value="butler" {{ request('concept_type') == 'butler' ? 'selected' : '' }}>執事喫茶</option>
                                <option value="idol" {{ request('concept_type') == 'idol' ? 'selected' : '' }}>アイドルカフェ</option>
                                <option value="cosplay" {{ request('concept_type') == 'cosplay' ? 'selected' : '' }}>コスプレカフェ</option>
                                <option value="other" {{ request('concept_type') == 'other' ? 'selected' : '' }}>その他</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="salary_min" class="form-label">最低給与</label>
                            <select class="form-select" id="salary_min" name="salary_min">
                                <option value="">指定なし</option>
                                <option value="800" {{ request('salary_min') == '800' ? 'selected' : '' }}>800円以上</option>
                                <option value="900" {{ request('salary_min') == '900' ? 'selected' : '' }}>900円以上</option>
                                <option value="1000" {{ request('salary_min') == '1000' ? 'selected' : '' }}>1,000円以上</option>
                                <option value="1200" {{ request('salary_min') == '1200' ? 'selected' : '' }}>1,200円以上</option>
                                <option value="1500" {{ request('salary_min') == '1500' ? 'selected' : '' }}>1,500円以上</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_type" class="form-label">雇用形態</label>
                            <select class="form-select" id="job_type" name="job_type">
                                <option value="">すべて</option>
                                <option value="part_time" {{ request('job_type') == 'part_time' ? 'selected' : '' }}>アルバイト</option>
                                <option value="full_time" {{ request('job_type') == 'full_time' ? 'selected' : '' }}>正社員</option>
                                <option value="contract" {{ request('job_type') == 'contract' ? 'selected' : '' }}>契約社員</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gender_requirement" class="form-label">性別</label>
                            <select class="form-select" id="gender_requirement" name="gender_requirement">
                                <option value="">すべて</option>
                                <option value="male" {{ request('gender_requirement') == 'male' ? 'selected' : '' }}>男性</option>
                                <option value="female" {{ request('gender_requirement') == 'female' ? 'selected' : '' }}>女性</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>検索
                            </button>
                            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>リセット
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- メインコンテンツ -->
        <div class="col-lg-9">
            <!-- 検索結果ヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">求人検索結果</h2>
                    <p class="text-muted mb-0">
                        {{ number_format($jobs->total()) }}件の求人が見つかりました
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0">並び順:</label>
                    <select class="form-select" id="sort" style="width: auto;" onchange="sortResults(this.value)">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>新着順</option>
                        <option value="salary" {{ request('sort') == 'salary' ? 'selected' : '' }}>給与順</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>人気順</option>
                        <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>締切順</option>
                    </select>
                </div>
            </div>
            
            <!-- 求人一覧 -->
            @if($jobs->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">該当する求人が見つかりませんでした</h4>
                    <p class="text-muted">検索条件を変更して再度お試しください。</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>条件をリセット
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($jobs as $job)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 job-card-modern">
                                @if($job->shop->image_url)
                                    <img src="{{ $job->shop->image_url }}" 
                                         class="card-img-top" alt="{{ $job->shop->name }}"
                                         style="height: 200px; object-fit: cover;"
                                         loading="lazy">
                                @else
                                    {{-- 画像がない場合はプレースホルダー背景を表示 --}}
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-store fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('jobs.show', $job->id) }}" 
                                               class="text-decoration-none">
                                                {{ $job->title }}
                                            </a>
                                        </h5>
                                        <span class="badge badge-concept">
                                            {{ $job->shop->concept_type }}
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-store me-1"></i>
                                        {{ $job->shop->name }}
                                    </p>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}
                                    </p>
                                    
                                    @if($job->description)
                                        <p class="card-text small">
                                            {{ \Illuminate\Support\Str::limit($job->description, 100) }}...
                                        </p>
                                    @endif
                                    
                                    @php
                                        $conditions = is_string($job->job_conditions) ? json_decode($job->job_conditions, true) : ($job->job_conditions ?? []);
                                        $conditionLabels = [
                                            'resume_not_required' => '履歴書不要',
                                            'weekly_once_ok' => '週1から勤務OK',
                                            'daily_payment_ok' => '日払い可',
                                            'transportation_fee' => '交通費支給',
                                            'no_experience_ok' => '未経験者OK',
                                            'student_welcome' => '大学生歓迎',
                                        ];
                                        $popularConditions = array_intersect($conditions, array_keys($conditionLabels));
                                    @endphp
                                    <div class="mb-2">
                                        @if($job->trial_visit_available)
                                            <span class="badge bg-success text-white me-1 mb-1 small">
                                                <i class="fas fa-door-open me-1"></i>体験入店可能
                                            </span>
                                        @endif
                                        @if(!empty($popularConditions))
                                            @foreach(array_slice($popularConditions, 0, 3) as $condition)
                                                <span class="badge bg-info text-white me-1 mb-1 small">
                                                    {{ $conditionLabels[$condition] }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            @if($job->salary_min)
                                                <span class="badge badge-salary">
                                                    {{ number_format($job->salary_min) }}円〜
                                                    @if($job->salary_max)
                                                        {{ number_format($job->salary_max) }}円
                                                    @endif
                                                </span>
                                            @endif
                                            <small class="text-muted">
                                                {{ $job->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('jobs.show', $job->id) }}" 
                                               class="btn btn-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>詳細を見る
                                            </a>
                                            @if(config('feature_flags.keep', false))
                                                @auth
                                                    @php
                                                        $job_is_kept = in_array($job->id, $keptJobIds ?? [], true);
                                                    @endphp
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm cj-keep-toggle flex-fill {{ $job_is_kept ? 'cj-keep-active' : '' }}"
                                                            data-target-type="job"
                                                            data-target-id="{{ $job->id }}"
                                                            data-kept="{{ $job_is_kept ? '1' : '0' }}"
                                                            aria-pressed="{{ $job_is_kept ? 'true' : 'false' }}">
                                                        <i class="{{ $job_is_kept ? 'fas' : 'far' }} fa-heart me-1"></i>
                                                        <span class="cj-keep-label">{{ $job_is_kept ? 'キープ中' : 'キープ' }}</span>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm flex-fill"
                                                            onclick="cjRequireLoginModal()">
                                                        <i class="far fa-heart me-1"></i>キープ
                                                    </button>
                                                @endauth
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- ページネーション -->
                @if($jobs->hasPages())
                    <nav aria-label="求人検索結果のページネーション" class="mt-4">
                        {{ $jobs->links() }}
                    </nav>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function sortResults(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}
</script>
@endpush
@endsection

