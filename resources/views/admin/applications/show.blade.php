@extends('layouts.app')

@section('title', '応募詳細 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-file-alt me-2"></i>応募詳細
                </h1>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>応募一覧に戻る
                </a>
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
                                    <td>{{ $application->job->title }}</td>
                                </tr>
                                <tr>
                                    <th>店舗</th>
                                    <td>{{ $application->job->shop->name }}</td>
                                </tr>
                                <tr>
                                    <th>応募者</th>
                                    <td>
                                        {{ $application->user->username }}<br>
                                        <small class="text-muted">{{ $application->user->last_name }} {{ $application->user->first_name }}</small><br>
                                        <small class="text-muted">{{ $application->user->email }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>応募メッセージ</th>
                                    <td>{{ $application->message ?: '-' }}</td>
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
                            <form method="POST" action="{{ route('admin.applications.update-status', $application->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">ステータス変更</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>審査中</option>
                                        <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>採用</option>
                                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>不採用</option>
                                        <option value="cancelled" {{ $application->status === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">ステータスを更新</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

