@extends('layouts.shop-admin')

@section('title', '応募詳細')

@section('content')
<!-- 店舗管理者ナビゲーション -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('shop-admin.dashboard') }}">
            <i class="fas fa-coffee me-2"></i>カフェJob
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="{{ route('shop-admin.applications.index') }}">
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
                    <i class="fas fa-file-alt me-2"></i>応募詳細
                </h1>
                <div>
                    <a href="{{ route('shop-admin.applications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>応募一覧に戻る
                    </a>
                    <a href="{{ route('shop-admin.chat.show', ['application_id' => $application->id]) }}" class="btn btn-info ms-2">
                        <i class="fas fa-comments me-1"></i>チャット
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">応募ID: {{ $application->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $application->id }}</td>
                                </tr>
                                <tr>
                                    <th>求人</th>
                                    <td>
                                        <strong>{{ $application->job->title }}</strong>
                                        <div class="mt-2">
                                            <a href="{{ route('shop-admin.jobs.edit', $application->job_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i>求人を編集
                                            </a>
                                            <a href="{{ route('shop-admin.jobs.index') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-list me-1"></i>求人一覧
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>応募者</th>
                                    <td>
                                        {{ $application->user->last_name }} {{ $application->user->first_name }}<br>
                                        <small class="text-muted">{{ $application->user->username }}</small><br>
                                        <small class="text-muted">{{ $application->user->email }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>応募メッセージ</th>
                                    <td>{{ $application->message ? nl2br(e($application->message)) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ステータス</th>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $application->status === 'accepted' ? 'success' : 
                                            ($application->status === 'rejected' ? 'danger' : 
                                            ($application->status === 'cancelled' ? 'secondary' : 'warning')) 
                                        }}">
                                            @php
                                                $statusLabels = [
                                                    'pending' => '審査中',
                                                    'accepted' => '採用',
                                                    'rejected' => '不採用',
                                                    'cancelled' => 'キャンセル'
                                                ];
                                            @endphp
                                            {{ $statusLabels[$application->status] ?? $application->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>応募日時</th>
                                    <td>{{ $application->applied_at->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('shop-admin.applications.update-status', $application->id) }}">
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">ステータス変更</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">ステータス</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>審査中</option>
                                                <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>採用</option>
                                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>不採用</option>
                                                <option value="cancelled" {{ $application->status === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>ステータスを更新
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- 応募禁止状況 -->
                            @php
                                $activeBan = \App\Models\UserApplicationBan::where('user_id', $application->user_id)
                                    ->where('shop_id', $application->job->shop_id)
                                    ->where('status', 'active')
                                    ->where('banned_until', '>', now())
                                    ->first();
                            @endphp
                            @if($activeBan)
                                <div class="card border-danger mt-3">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-ban me-2"></i>応募禁止中
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small mb-2">
                                            <strong>禁止期限:</strong> {{ $activeBan->banned_until->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}まで
                                        </p>
                                        @if($activeBan->reason)
                                            <p class="small mb-0">
                                                <strong>理由:</strong> {{ $activeBan->reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- 求職者報告フォーム -->
                            @if(isset($existingReport) && $existingReport)
                                <div class="card border-success mt-3">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-check-circle me-2"></i>報告済み
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small mb-2">
                                            <strong>報告タイプ:</strong> 
                                            @php
                                                $reportTypeLabels = [
                                                    'no_show' => '面接ドタキャン',
                                                    'inappropriate_behavior' => '不適切な行動',
                                                    'false_information' => '虚偽の情報',
                                                    'other' => 'その他',
                                                ];
                                            @endphp
                                            {{ $reportTypeLabels[$existingReport->report_type] ?? $existingReport->report_type }}
                                        </p>
                                        <p class="small mb-2">
                                            <strong>報告内容:</strong> {{ $existingReport->message }}
                                        </p>
                                        <p class="small mb-0 text-muted">
                                            報告日時: {{ $existingReport->created_at->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                            <form method="POST" action="{{ route('shop-admin.applications.report', $application->id) }}" class="mt-3">
                                @csrf
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>求職者を報告
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted mb-3">
                                            面接ドタキャン、不誠実な対応など、問題のある求職者をシステム管理者に報告できます。
                                        </p>
                                        <div class="mb-3">
                                            <label for="report_type" class="form-label">報告タイプ</label>
                                            <select class="form-select form-select-sm" id="report_type" name="report_type" required>
                                                <option value="">選択してください</option>
                                                <option value="no_show" {{ old('report_type') === 'no_show' ? 'selected' : '' }}>面接ドタキャン</option>
                                                <option value="inappropriate_behavior" {{ old('report_type') === 'inappropriate_behavior' ? 'selected' : '' }}>不適切な行動</option>
                                                <option value="false_information" {{ old('report_type') === 'false_information' ? 'selected' : '' }}>虚偽の情報</option>
                                                <option value="other" {{ old('report_type') === 'other' ? 'selected' : '' }}>その他</option>
                                            </select>
                                            @error('report_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="report_message" class="form-label">報告内容 <span class="text-danger">*</span></label>
                                            <textarea class="form-control form-control-sm @error('report_message') is-invalid @enderror" 
                                                      id="report_message" name="report_message" rows="2" maxlength="30" required
                                                      placeholder="例: 面接当日に無断欠席">{{ old('report_message') }}</textarea>
                                            <small class="form-text text-muted">
                                                <span id="report_message_count">0</span>/30文字
                                            </small>
                                            @error('report_message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm w-100">
                                            <i class="fas fa-paper-plane me-1"></i>報告を送信
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endif

                            <!-- 応募禁止ボタン -->
                            @if(!$activeBan)
                                <form method="POST" action="{{ route('shop-admin.applications.ban', $application->id) }}" class="mt-3" 
                                      onsubmit="return confirm('この求職者を半年間応募禁止にしますか？この操作は取り消せません。');">
                                    @csrf
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-ban me-2"></i>応募禁止設定
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="small text-muted mb-3">
                                                この求職者を半年間（6ヶ月間）この店舗への応募を禁止します。嫌がらせや繰り返しのドタキャンなど、問題のある求職者に対して使用してください。
                                            </p>
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="fas fa-ban me-1"></i>半年間応募禁止にする
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('report_message');
    const countSpan = document.getElementById('report_message_count');
    
    if (messageTextarea && countSpan) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            countSpan.textContent = length;
            if (length > 30) {
                countSpan.classList.add('text-danger');
            } else {
                countSpan.classList.remove('text-danger');
            }
        });
        
        // 初期カウント
        countSpan.textContent = messageTextarea.value.length;
    }
});
</script>
@endpush
@endsection

