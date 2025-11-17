@extends('layouts.shop-admin')

@section('title', '求人編集')

@section('content')
<!-- 店舗管理者ナビゲーション -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('shop-admin.dashboard') }}">
            <i class="fas fa-coffee me-2"></i>カフェコレ（CafeColle）
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('shop-admin.jobs.index') }}">
                <i class="fas fa-briefcase me-1"></i>求人管理
            </a>
            <a class="nav-link" href="{{ route('shop-admin.applications.index') }}">
                <i class="fas fa-file-alt me-1"></i>応募管理
            </a>
            <a class="nav-link" href="{{ route('shop-admin.chat.index') }}">
                <i class="fas fa-comments me-1"></i>チャット
            </a>
            <a class="nav-link" href="{{ route('shop-admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-1"></i>ログアウト
            </a>
            <form id="logout-form" action="{{ route('shop-admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit me-2"></i>求人情報を編集
                </h1>
                <a href="{{ route('shop-admin.jobs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>求人一覧に戻る
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('shop-admin.jobs.update', $job->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- 基本情報 -->
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>基本情報
                        </h5>

                        <div class="mb-3">
                            <label for="title" class="form-label">求人タイトル <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                                   value="{{ old('title', $job->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">仕事内容</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $job->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">応募条件</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="3">{{ old('requirements', $job->requirements) }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">福利厚生・特典</label>
                            <textarea class="form-control @error('benefits') is-invalid @enderror" id="benefits" name="benefits" rows="3">{{ old('benefits', $job->benefits) }}</textarea>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 勤務条件 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-clock me-2"></i>勤務条件
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="job_type" class="form-label">雇用形態 <span class="text-danger">*</span></label>
                                <select class="form-select @error('job_type') is-invalid @enderror" id="job_type" name="job_type" required>
                                    <option value="part_time" {{ old('job_type', $job->job_type) === 'part_time' ? 'selected' : '' }}>パート・アルバイト</option>
                                    <option value="full_time" {{ old('job_type', $job->job_type) === 'full_time' ? 'selected' : '' }}>正社員</option>
                                    <option value="contract" {{ old('job_type', $job->job_type) === 'contract' ? 'selected' : '' }}>契約社員</option>
                                </select>
                                @error('job_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">ステータス <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $job->status) === 'active' ? 'selected' : '' }}>公開中</option>
                                    <option value="inactive" {{ old('status', $job->status) === 'inactive' ? 'selected' : '' }}>非公開</option>
                                    <option value="closed" {{ old('status', $job->status) === 'closed' ? 'selected' : '' }}>終了</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="work_hours" class="form-label">勤務時間</label>
                            <textarea class="form-control @error('work_hours') is-invalid @enderror" id="work_hours" name="work_hours" rows="2">{{ old('work_hours', $job->work_hours) }}</textarea>
                            @error('work_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salary_min" class="form-label">給与（最低額）</label>
                                <input type="number" class="form-control @error('salary_min') is-invalid @enderror" id="salary_min" name="salary_min" 
                                       value="{{ old('salary_min', $job->salary_min) }}" min="0">
                                @error('salary_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="salary_max" class="form-label">給与（最高額）</label>
                                <input type="number" class="form-control @error('salary_max') is-invalid @enderror" id="salary_max" name="salary_max" 
                                       value="{{ old('salary_max', $job->salary_max) }}" min="0">
                                @error('salary_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- 待遇・条件 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-star me-2"></i>待遇・条件
                        </h5>
                        <p class="text-muted small mb-3">該当する条件にチェックを入れてください</p>

                        @php
                            $currentConditions = old('job_conditions', $job->job_conditions ?? []);
                            if (is_string($currentConditions)) {
                                $currentConditions = json_decode($currentConditions, true) ?? [];
                            }
                        @endphp

                        <div class="row">
                            <!-- 応募・面接関連 -->
                            <div class="col-md-6 mb-4">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-file-alt me-1"></i>応募・面接関連
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_resume_not_required" 
                                           name="job_conditions[]" value="resume_not_required"
                                           {{ in_array('resume_not_required', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_resume_not_required">
                                        履歴書不要
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_friend_application_ok" 
                                           name="job_conditions[]" value="friend_application_ok"
                                           {{ in_array('friend_application_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_friend_application_ok">
                                        友達と応募可
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_online_interview_ok" 
                                           name="job_conditions[]" value="online_interview_ok"
                                           {{ in_array('online_interview_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_online_interview_ok">
                                        メール・LINE面接可
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_observation_ok" 
                                           name="job_conditions[]" value="observation_ok"
                                           {{ in_array('observation_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_observation_ok">
                                        見学可(勤務無し)
                                    </label>
                                </div>
                            </div>

                            <!-- 環境・勤務関連 -->
                            <div class="col-md-6 mb-4">
                                <h6 class="fw-bold mb-3 text-success">
                                    <i class="fas fa-users me-1"></i>環境・勤務関連
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_no_experience_ok" 
                                           name="job_conditions[]" value="no_experience_ok"
                                           {{ in_array('no_experience_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_no_experience_ok">
                                        未経験者OK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_weekly_once_ok" 
                                           name="job_conditions[]" value="weekly_once_ok"
                                           {{ in_array('weekly_once_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_weekly_once_ok">
                                        週1から勤務OK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_no_photo_required" 
                                           name="job_conditions[]" value="no_photo_required"
                                           {{ in_array('no_photo_required', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_no_photo_required">
                                        顔写真載せないでOK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_costume_provided" 
                                           name="job_conditions[]" value="costume_provided"
                                           {{ in_array('costume_provided', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_costume_provided">
                                        コスチューム支給
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_student_welcome" 
                                           name="job_conditions[]" value="student_welcome"
                                           {{ in_array('student_welcome', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_student_welcome">
                                        大学生歓迎
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_no_alcohol_ok" 
                                           name="job_conditions[]" value="no_alcohol_ok"
                                           {{ in_array('no_alcohol_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_no_alcohol_ok">
                                        お酒飲めなくてOK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_no_complicated_relationships" 
                                           name="job_conditions[]" value="no_complicated_relationships"
                                           {{ in_array('no_complicated_relationships', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_no_complicated_relationships">
                                        面倒な人間関係なし
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- お給料・待遇関連 -->
                            <div class="col-md-6 mb-4">
                                <h6 class="fw-bold mb-3 text-warning">
                                    <i class="fas fa-yen-sign me-1"></i>お給料・待遇関連
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_daily_payment_ok" 
                                           name="job_conditions[]" value="daily_payment_ok"
                                           {{ in_array('daily_payment_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_daily_payment_ok">
                                        日払い可
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_transportation_fee" 
                                           name="job_conditions[]" value="transportation_fee"
                                           {{ in_array('transportation_fee', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_transportation_fee">
                                        交通費支給
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_commission_available" 
                                           name="job_conditions[]" value="commission_available"
                                           {{ in_array('commission_available', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_commission_available">
                                        歩合アリ
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_full_daily_payment" 
                                           name="job_conditions[]" value="full_daily_payment"
                                           {{ in_array('full_daily_payment', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_full_daily_payment">
                                        全額日払い可
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_no_penalty" 
                                           name="job_conditions[]" value="no_penalty"
                                           {{ in_array('no_penalty', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_no_penalty">
                                        罰金なし
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_training_salary" 
                                           name="job_conditions[]" value="training_salary"
                                           {{ in_array('training_salary', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_training_salary">
                                        研修期間も給料支給
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_no_quota" 
                                           name="job_conditions[]" value="no_quota"
                                           {{ in_array('no_quota', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_no_quota">
                                        ノルマ無し
                                    </label>
                                </div>
                            </div>

                            <!-- 見た目・性格関連 -->
                            <div class="col-md-6 mb-4">
                                <h6 class="fw-bold mb-3 text-info">
                                    <i class="fas fa-palette me-1"></i>見た目・性格関連
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_flashy_hair_ok" 
                                           name="job_conditions[]" value="flashy_hair_ok"
                                           {{ in_array('flashy_hair_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_flashy_hair_ok">
                                        派手な髪色OK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_nail_art_ok" 
                                           name="job_conditions[]" value="nail_art_ok"
                                           {{ in_array('nail_art_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_nail_art_ok">
                                        ネイルアートOK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_many_piercings_ok" 
                                           name="job_conditions[]" value="many_piercings_ok"
                                           {{ in_array('many_piercings_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_many_piercings_ok">
                                        ピアス多数でもOK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_tattoo_ok" 
                                           name="job_conditions[]" value="tattoo_ok"
                                           {{ in_array('tattoo_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_tattoo_ok">
                                        タトゥーOK
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- その他 -->
                            <div class="col-md-6 mb-4">
                                <h6 class="fw-bold mb-3 text-secondary">
                                    <i class="fas fa-ellipsis-h me-1"></i>その他
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_non_otaku_ok" 
                                           name="job_conditions[]" value="non_otaku_ok"
                                           {{ in_array('non_otaku_ok', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_non_otaku_ok">
                                        オタク以外もOK
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_new_shop" 
                                           name="job_conditions[]" value="new_shop"
                                           {{ in_array('new_shop', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_new_shop">
                                        新規オープン店
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="condition_female_staff_manager" 
                                           name="job_conditions[]" value="female_staff_manager"
                                           {{ in_array('female_staff_manager', $currentConditions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="condition_female_staff_manager">
                                        女性社員・店長
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 制服・コスプレ情報 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-tshirt me-2"></i>制服・コスプレ情報
                        </h5>
                        <p class="text-muted small mb-3">この求人で使用する制服やコスプレについて詳しく説明してください</p>

                        @php
                            $currentUniformImages = old('uniform_images', $job->uniform_images ?? []);
                            if (is_string($currentUniformImages)) {
                                $currentUniformImages = json_decode($currentUniformImages, true) ?? [];
                            }
                            if (!is_array($currentUniformImages)) {
                                $currentUniformImages = [];
                            }
                        @endphp

                        <div class="mb-3">
                            <label for="uniform_description" class="form-label">制服・コスプレの説明</label>
                            <textarea class="form-control @error('uniform_description') is-invalid @enderror" 
                                      id="uniform_description" name="uniform_description" rows="4"
                                      placeholder="例: 可愛らしいメイド服を着用していただきます。季節に応じて衣装が変わります。">{{ old('uniform_description', $job->uniform_description) }}</textarea>
                            <small class="form-text text-muted">制服やコスプレの特徴、種類、着用時の注意点などを記載してください</small>
                            @error('uniform_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="uniform_images" class="form-label">制服・コスプレ画像URL</label>
                            <div id="uniform-images-container">
                                @if(!empty($currentUniformImages))
                                    @foreach($currentUniformImages as $index => $imageUrl)
                                        <div class="input-group mb-2">
                                            <input type="url" class="form-control @error('uniform_images.' . $index) is-invalid @enderror" 
                                                   name="uniform_images[]" 
                                                   placeholder="https://example.com/image{{ $index + 1 }}.jpg"
                                                   value="{{ old('uniform_images.' . $index, $imageUrl) }}">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeUniformImage(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="url" class="form-control @error('uniform_images.0') is-invalid @enderror" 
                                               name="uniform_images[]" 
                                               placeholder="https://example.com/image1.jpg"
                                               value="{{ old('uniform_images.0') }}">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeUniformImage(this)" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addUniformImage()">
                                <i class="fas fa-plus me-1"></i>画像URLを追加
                            </button>
                            <small class="form-text text-muted d-block mt-2">制服やコスプレの画像URLを追加できます（最大5枚）</small>
                            @error('uniform_images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 体験入店 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-door-open me-2"></i>体験入店
                        </h5>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="trial_visit_available" 
                                       name="trial_visit_available" value="1"
                                       {{ old('trial_visit_available', $job->trial_visit_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="trial_visit_available">
                                    <strong>体験入店可能</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                この求人では体験入店（見学・トライアル）を受け付けます
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender_requirement" class="form-label">性別要件 <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender_requirement') is-invalid @enderror" id="gender_requirement" name="gender_requirement" required>
                                    <option value="any" {{ old('gender_requirement', $job->gender_requirement) === 'any' ? 'selected' : '' }}>不問</option>
                                    <option value="male" {{ old('gender_requirement', $job->gender_requirement) === 'male' ? 'selected' : '' }}>男性</option>
                                    <option value="female" {{ old('gender_requirement', $job->gender_requirement) === 'female' ? 'selected' : '' }}>女性</option>
                                </select>
                                @error('gender_requirement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="application_deadline" class="form-label">応募締切</label>
                                <input type="date" class="form-control @error('application_deadline') is-invalid @enderror" id="application_deadline" name="application_deadline" 
                                       value="{{ old('application_deadline', $job->application_deadline ? \Carbon\Carbon::parse($job->application_deadline)->format('Y-m-d') : '') }}">
                                @error('application_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="age_min" class="form-label">年齢（最低）</label>
                                <input type="number" class="form-control @error('age_min') is-invalid @enderror" id="age_min" name="age_min" 
                                       value="{{ old('age_min', $job->age_min) }}" min="0">
                                @error('age_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="age_max" class="form-label">年齢（最高）</label>
                                <input type="number" class="form-control @error('age_max') is-invalid @enderror" id="age_max" name="age_max" 
                                       value="{{ old('age_max', $job->age_max) }}" min="0">
                                @error('age_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>求人情報を更新
                            </button>
                            <a href="{{ route('shop-admin.jobs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>求人一覧に戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let uniformImageCount = {{ count($currentUniformImages) > 0 ? count($currentUniformImages) : 1 }};
const maxUniformImages = 5;

function addUniformImage() {
    if (uniformImageCount >= maxUniformImages) {
        alert('画像は最大' + maxUniformImages + '枚まで追加できます');
        return;
    }
    
    const container = document.getElementById('uniform-images-container');
    const newInput = document.createElement('div');
    newInput.className = 'input-group mb-2';
    newInput.innerHTML = `
        <input type="url" class="form-control" 
               name="uniform_images[]" 
               placeholder="https://example.com/image${uniformImageCount + 1}.jpg">
        <button type="button" class="btn btn-outline-danger" onclick="removeUniformImage(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(newInput);
    uniformImageCount++;
    updateRemoveButtons();
}

function removeUniformImage(button) {
    button.closest('.input-group').remove();
    uniformImageCount--;
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const buttons = document.querySelectorAll('#uniform-images-container .btn-outline-danger');
    buttons.forEach(btn => {
        btn.style.display = uniformImageCount > 1 ? 'block' : 'none';
    });
}

// 初期状態の処理
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
});
</script>
@endpush
@endsection

