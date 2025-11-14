@extends('layouts.app')

@section('title', '求職者報告詳細')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>求職者報告詳細
                </h1>
                <a href="{{ route('admin.user-reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>報告一覧に戻る
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">報告情報</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>報告ID</th>
                            <td>{{ $report->id }}</td>
                        </tr>
                        <tr>
                            <th>報告タイプ</th>
                            <td>
                                <span class="badge bg-info">
                                    {{ $reportTypeLabels[$report->report_type] ?? $report->report_type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>報告内容</th>
                            <td>{{ $report->message }}</td>
                        </tr>
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <span class="badge bg-{{ 
                                    $report->status === 'pending' ? 'warning' : 
                                    ($report->status === 'reviewed' ? 'info' : 
                                    ($report->status === 'resolved' ? 'success' : 'secondary')) 
                                }}">
                                    {{ $statusLabels[$report->status] ?? $report->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>報告日時</th>
                            <td>{{ $report->created_at->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}</td>
                        </tr>
                        @if($report->reviewed_at)
                        <tr>
                            <th>確認日時</th>
                            <td>{{ $report->reviewed_at->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}</td>
                        </tr>
                        @endif
                        @if($report->reviewedBy)
                        <tr>
                            <th>確認者</th>
                            <td>{{ $report->reviewedBy->username }}</td>
                        </tr>
                        @endif
                        @if($report->admin_notes)
                        <tr>
                            <th>管理者メモ</th>
                            <td>{{ $report->admin_notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">応募情報</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>応募ID</th>
                            <td>{{ $report->application->id }}</td>
                        </tr>
                        <tr>
                            <th>求人</th>
                            <td>
                                <strong>{{ $report->application->job->title }}</strong><br>
                                <small class="text-muted">{{ $report->application->job->shop->name ?? '-' }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>応募メッセージ</th>
                            <td>{{ $report->application->message ? nl2br(e($report->application->message)) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>応募ステータス</th>
                            <td>
                                <span class="badge bg-{{ 
                                    $report->application->status === 'accepted' ? 'success' : 
                                    ($report->application->status === 'rejected' ? 'danger' : 
                                    ($report->application->status === 'cancelled' ? 'secondary' : 'warning')) 
                                }}">
                                    @php
                                        $statusLabels = [
                                            'pending' => '審査中',
                                            'accepted' => '採用',
                                            'rejected' => '不採用',
                                            'cancelled' => 'キャンセル'
                                        ];
                                    @endphp
                                    {{ $statusLabels[$report->application->status] ?? $report->application->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>応募日時</th>
                            <td>{{ $report->application->applied_at->setTimezone('Asia/Tokyo')->format('Y年m月d日 H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">報告された求職者</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>ユーザー名</th>
                            <td>{{ $report->user->username }}</td>
                        </tr>
                        <tr>
                            <th>メール</th>
                            <td>{{ $report->user->email }}</td>
                        </tr>
                        <tr>
                            <th>名前</th>
                            <td>{{ $report->user->last_name }} {{ $report->user->first_name }}</td>
                        </tr>
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <span class="badge bg-{{ $report->user->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $report->user->status === 'active' ? '有効' : '無効' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                    <a href="{{ route('admin.users.show', $report->user->id) }}" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-user me-1"></i>ユーザー詳細を見る
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">報告した店舗管理者</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>ユーザー名</th>
                            <td>{{ $report->shopAdmin->username }}</td>
                        </tr>
                        <tr>
                            <th>メール</th>
                            <td>{{ $report->shopAdmin->email }}</td>
                        </tr>
                        <tr>
                            <th>店舗</th>
                            <td>{{ $report->shopAdmin->shop->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ステータス更新フォーム -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">ステータス更新</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.user-reports.update-status', $report->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">ステータス</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>未確認</option>
                                <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>確認済み</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>対応済み</option>
                                <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>却下</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">管理者メモ</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4" 
                                      placeholder="対応内容やメモを記入してください">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                            <small class="form-text text-muted">最大500文字</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>ステータスを更新
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

