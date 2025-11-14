@extends('layouts.app')

@section('title', $job->title . ' - 求人詳細')
@section('description', $job->description ? \Illuminate\Support\Str::limit($job->description, 100) : '求人の詳細情報を表示します。')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- メインコンテンツ -->
        <div class="col-lg-8">
            <!-- パンくずリスト -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">求人検索</a></li>
                    <li class="breadcrumb-item active">{{ $job->title }}</li>
                </ol>
            </nav>
            
            <!-- 求人詳細 -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h1 class="h4 mb-1">{{ $job->title }}</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-store me-1"></i>
                                {{ $job->shop->name }}
                            </p>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <span class="badge bg-primary fs-6">{{ $job->shop->concept_type }}</span>
                            @if(config('feature_flags.keep', false))
                                @auth
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm cj-keep-toggle {{ $isKept ? 'cj-keep-active' : '' }}"
                                            data-target-type="job"
                                            data-target-id="{{ $job->id }}"
                                            data-kept="{{ $isKept ? '1' : '0' }}"
                                            aria-pressed="{{ $isKept ? 'true' : 'false' }}">
                                        <i class="{{ $isKept ? 'fas' : 'far' }} fa-heart me-1"></i>
                                        <span class="cj-keep-label">{{ $isKept ? 'キープ中' : 'キープ' }}</span>
                                    </button>
                                @else
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="cjRequireLoginModal()">
                                        <i class="far fa-heart me-1"></i>キープ
                                    </button>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- 待遇・条件（目立つ位置に配置） -->
                    @php
                        $conditions = is_string($job->job_conditions) ? json_decode($job->job_conditions, true) : ($job->job_conditions ?? []);
                        $conditionLabels = [
                            'resume_not_required' => '履歴書不要',
                            'friend_application_ok' => '友達と応募可',
                            'online_interview_ok' => 'メール・LINE面接可',
                            'observation_ok' => '見学可(勤務無し)',
                            'no_experience_ok' => '未経験者OK',
                            'weekly_once_ok' => '週1から勤務OK',
                            'no_photo_required' => '顔写真載せないでOK',
                            'costume_provided' => 'コスチューム支給',
                            'student_welcome' => '大学生歓迎',
                            'no_alcohol_ok' => 'お酒飲めなくてOK',
                            'no_complicated_relationships' => '面倒な人間関係なし',
                            'daily_payment_ok' => '日払い可',
                            'transportation_fee' => '交通費支給',
                            'commission_available' => '歩合アリ',
                            'full_daily_payment' => '全額日払い可',
                            'no_penalty' => '罰金なし',
                            'training_salary' => '研修期間も給料支給',
                            'no_quota' => 'ノルマ無し',
                            'flashy_hair_ok' => '派手な髪色OK',
                            'nail_art_ok' => 'ネイルアートOK',
                            'many_piercings_ok' => 'ピアス多数でもOK',
                            'tattoo_ok' => 'タトゥーOK',
                            'non_otaku_ok' => 'オタク以外もOK',
                            'new_shop' => '新規オープン店',
                            'female_staff_manager' => '女性社員・店長',
                        ];
                    @endphp
                    @if(!empty($conditions) || $job->trial_visit_available)
                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="mb-3">
                            <i class="fas fa-star me-2 text-warning"></i>注目の待遇・条件
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            @if($job->trial_visit_available)
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-door-open me-1"></i>体験入店可能
                                </span>
                            @endif
                            @foreach($conditions as $condition)
                                @if(isset($conditionLabels[$condition]))
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>{{ $conditionLabels[$condition] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- 基本情報 -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">基本情報</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>雇用形態</strong></td>
                                    <td>
                                        @if($job->job_type === 'part_time')パート・アルバイト
                                        @elseif($job->job_type === 'full_time')正社員
                                        @elseif($job->job_type === 'contract')契約社員
                                        @else{{ $job->job_type }}
                                        @endif
                                    </td>
                                </tr>
                                @if($job->trial_visit_available)
                                <tr>
                                    <td><strong>体験入店</strong></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>体験入店可能
                                        </span>
                                    </td>
                                </tr>
                                @endif
                                @if($job->salary_min)
                                <tr>
                                    <td><strong>給与</strong></td>
                                    <td>
                                        {{ number_format($job->salary_min) }}円〜
                                        @if($job->salary_max)
                                            {{ number_format($job->salary_max) }}円
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>性別</strong></td>
                                    <td>
                                        @if($job->gender_requirement === 'any')不問
                                        @elseif($job->gender_requirement === 'male')男性
                                        @elseif($job->gender_requirement === 'female')女性
                                        @else{{ $job->gender_requirement }}
                                        @endif
                                    </td>
                                </tr>
                                @if($job->age_min || $job->age_max)
                                <tr>
                                    <td><strong>年齢</strong></td>
                                    <td>
                                        @if($job->age_min){{ $job->age_min }}歳〜@endif
                                        @if($job->age_max){{ $job->age_max }}歳@endif
                                    </td>
                                </tr>
                                @endif
                                @if($job->application_deadline)
                                <tr>
                                    <td><strong>応募締切</strong></td>
                                    <td>{{ $job->application_deadline->format('Y年m月d日') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">勤務時間</h5>
                            <div class="bg-light p-3 rounded">
                                @if($job->work_hours)
                                    <pre class="mb-0">{{ $job->work_hours }}</pre>
                                @else
                                    <p class="text-muted mb-0">記載なし</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- 求人内容 -->
                    @if($job->description)
                    <div class="mb-4">
                        <h5 class="mb-3">求人内容</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- 応募条件 -->
                    @if($job->requirements)
                    <div class="mb-4">
                        <h5 class="mb-3">応募条件</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($job->requirements)) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- 福利厚生 -->
                    @if($job->benefits)
                    <div class="mb-4">
                        <h5 class="mb-3">福利厚生</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($job->benefits)) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- 制服・コスプレ情報 -->
                    @php
                        $uniformImages = is_string($job->uniform_images) ? json_decode($job->uniform_images, true) : ($job->uniform_images ?? []);
                        if (!is_array($uniformImages)) {
                            $uniformImages = [];
                        }
                    @endphp
                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="mb-3">
                            <i class="fas fa-tshirt me-2 text-primary"></i>制服・コスプレ情報
                        </h5>
                        @if($job->uniform_description)
                            <div class="mb-3">
                                <p class="mb-0">{!! nl2br(e($job->uniform_description)) !!}</p>
                            </div>
                        @else
                            <p class="text-muted mb-3">制服・コスプレの詳細情報はありません</p>
                        @endif
                        @if(!empty($uniformImages))
                            <div class="row g-3">
                                @foreach($uniformImages as $imageUrl)
                                    @if(!empty(trim($imageUrl)))
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card">
                                                <img src="{{ trim($imageUrl) }}" 
                                                     class="card-img-top" 
                                                     alt="制服・コスプレ画像"
                                                     style="height: 200px; object-fit: cover; cursor: pointer;"
                                                     loading="lazy"
                                                     onclick="window.open(this.src, '_blank')"
                                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3E画像が読み込めません%3C/text%3E%3C/svg%3E';">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">制服・コスプレの画像はありません</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- 店舗情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>店舗情報
                    </h5>
                </div>
                @if($job->shop->image_url)
                    <img src="{{ $job->shop->image_url }}" 
                         class="card-img-top" 
                         alt="{{ $job->shop->name }}" 
                         style="max-height: 300px; object-fit: cover;"
                         loading="lazy">
                @else
                    {{-- 画像がない場合はプレースホルダー背景を表示 --}}
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="min-height: 200px;">
                        <div class="text-center">
                            <i class="fas fa-store fa-3x text-muted mb-2"></i>
                            <p class="text-muted small mb-0">画像がありません</p>
                        </div>
                    </div>
                @endif
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>{{ $job->shop->name }}</h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $job->shop->prefecture->name ?? '' }}{{ $job->shop->city->name ?? '' }}{{ $job->shop->address }}
                            </p>
                            @if($job->shop->phone)
                            <p class="text-muted mb-2">
                                <i class="fas fa-phone me-1"></i>
                                {{ $job->shop->phone }}
                            </p>
                            @endif
                            @if($job->shop->website)
                            <p class="text-muted mb-2">
                                <i class="fas fa-globe me-1"></i>
                                <a href="{{ $job->shop->website }}" target="_blank">
                                    {{ $job->shop->website }}
                                </a>
                            </p>
                            @endif
                            @if($job->shop->opening_hours)
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock me-1"></i>
                                <pre class="mb-0 small">{{ $job->shop->opening_hours }}</pre>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- キャスト情報 -->
            @if($casts->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>キャスト情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($casts as $cast)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                @if($cast->profile_image)
                                    <img src="{{ $cast->profile_image }}" 
                                         class="card-img-top" alt="{{ $cast->name }}">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ $cast->name }}</h6>
                                    @if($cast->nickname)
                                        <p class="card-text small text-muted">
                                            ニックネーム: {{ $cast->nickname }}
                                        </p>
                                    @endif
                                    @if($cast->age)
                                        <p class="card-text small">年齢: {{ $cast->age }}歳</p>
                                    @endif
                                    @if($cast->hobby)
                                        <p class="card-text small">趣味: {{ $cast->hobby }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- 関連求人 -->
            @if($relatedJobs->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>同じ店舗の他の求人
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedJobs as $relatedJob)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('jobs.show', $relatedJob->id) }}" 
                                           class="text-decoration-none">
                                            {{ $relatedJob->title }}
                                        </a>
                                    </h6>
                                    <p class="card-text small text-muted">
                                        {{ $relatedJob->shop->prefecture->name ?? '' }}{{ $relatedJob->shop->city->name ?? '' }}
                                    </p>
                                    @if($relatedJob->salary_min)
                                        <span class="badge bg-primary">
                                            {{ number_format($relatedJob->salary_min) }}円〜
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
            <!-- 体験入店バナー -->
            @if($job->trial_visit_available)
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-door-open me-2"></i>体験入店可能
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 small">
                        この求人では体験入店（見学・トライアル）を受け付けています。実際の職場の雰囲気を体験してから応募を決められます。
                    </p>
                </div>
            </div>
            @endif

            <!-- 待遇・条件サマリー -->
            @php
                $sidebarConditions = is_string($job->job_conditions) ? json_decode($job->job_conditions, true) : ($job->job_conditions ?? []);
                $sidebarConditionLabels = [
                    'resume_not_required' => '履歴書不要',
                    'friend_application_ok' => '友達と応募可',
                    'online_interview_ok' => 'メール・LINE面接可',
                    'observation_ok' => '見学可(勤務無し)',
                    'no_experience_ok' => '未経験者OK',
                    'weekly_once_ok' => '週1から勤務OK',
                    'no_photo_required' => '顔写真載せないでOK',
                    'costume_provided' => 'コスチューム支給',
                    'student_welcome' => '大学生歓迎',
                    'no_alcohol_ok' => 'お酒飲めなくてOK',
                    'no_complicated_relationships' => '面倒な人間関係なし',
                    'daily_payment_ok' => '日払い可',
                    'transportation_fee' => '交通費支給',
                    'commission_available' => '歩合アリ',
                    'full_daily_payment' => '全額日払い可',
                    'no_penalty' => '罰金なし',
                    'training_salary' => '研修期間も給料支給',
                    'no_quota' => 'ノルマ無し',
                    'flashy_hair_ok' => '派手な髪色OK',
                    'nail_art_ok' => 'ネイルアートOK',
                    'many_piercings_ok' => 'ピアス多数でもOK',
                    'tattoo_ok' => 'タトゥーOK',
                    'non_otaku_ok' => 'オタク以外もOK',
                    'new_shop' => '新規オープン店',
                    'female_staff_manager' => '女性社員・店長',
                ];
                $popularConditions = array_slice($sidebarConditions, 0, 5);
            @endphp
            @if(!empty($popularConditions))
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-star me-2"></i>注目の待遇・条件
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        @foreach($popularConditions as $condition)
                            @if(isset($sidebarConditionLabels[$condition]))
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="small">{{ $sidebarConditionLabels[$condition] }}</span>
                                </div>
                            @endif
                        @endforeach
                        @if(count($sidebarConditions) > 5)
                            <small class="text-muted mt-2">
                                他{{ count($sidebarConditions) - 5 }}件の条件があります
                            </small>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- 応募フォーム -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>応募フォーム
                    </h5>
                </div>
                <div class="card-body">
                    @auth
                        <form method="POST" action="{{ route('applications.store') }}">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <div class="mb-3">
                                <label for="message" class="form-label">応募メッセージ</label>
                                <textarea class="form-control" id="message" name="message" rows="4" 
                                          placeholder="自己紹介や応募動機を記入してください"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-1"></i>応募する
                            </button>
                        </form>
                    @else
                        <p class="text-muted mb-3">応募するにはログインが必要です。</p>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-1"></i>ログイン
                        </a>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                アカウントをお持ちでない方は
                                <a href="{{ route('register') }}">新規登録</a>
                            </small>
                        </div>
                    @endauth
                </div>
            </div>
            
            <!-- キープ -->
            @if(config('feature_flags.keep', false))
            <div class="card mb-4">
                <div class="card-body text-center">
                    @auth
                        <button type="button"
                                class="btn btn-outline-danger cj-keep-toggle {{ $isKept ? 'cj-keep-active' : '' }}"
                                data-target-type="job"
                                data-target-id="{{ $job->id }}"
                                data-kept="{{ $isKept ? '1' : '0' }}"
                                aria-pressed="{{ $isKept ? 'true' : 'false' }}">
                            <i class="{{ $isKept ? 'fas' : 'far' }} fa-heart me-1"></i>
                            <span class="cj-keep-label">{{ $isKept ? 'キープ中' : 'お気に入りに追加' }}</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-danger" onclick="cjRequireLoginModal()">
                            <i class="far fa-heart me-1"></i>お気に入りに追加
                        </button>
                    @endauth
                    <p class="text-muted small mt-2 mb-0">気になる求人をキープしてマイページから比較できます。</p>
                </div>
            </div>
            @endif
            
            <!-- シェア -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-share-alt me-2"></i>シェア
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($job->title) }}&url={{ urlencode(route('jobs.show', $job->id)) }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('jobs.show', $job->id)) }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('URLをコピーしました');
    });
}
</script>
@endpush
@endsection

