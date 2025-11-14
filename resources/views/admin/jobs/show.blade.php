@extends('layouts.app')

@section('title', '求人詳細 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-briefcase me-2"></i>求人詳細
                </h1>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>求人一覧に戻る
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $job->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $job->id }}</td>
                                </tr>
                                <tr>
                                    <th>求人タイトル</th>
                                    <td>{{ $job->title }}</td>
                                </tr>
                                <tr>
                                    <th>店舗名</th>
                                    <td>{{ $job->shop->name }}</td>
                                </tr>
                                <tr>
                                    <th>給与</th>
                                    <td>
                                        @if($job->salary_min && $job->salary_max)
                                            {{ number_format($job->salary_min) }}円 - {{ number_format($job->salary_max) }}円
                                        @elseif($job->salary_min)
                                            {{ number_format($job->salary_min) }}円以上
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>ステータス</th>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $job->status === 'active' ? 'success' : 
                                            ($job->status === 'closed' ? 'danger' : 'secondary') 
                                        }}">
                                            {{ $job->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.jobs.update-status', $job->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">ステータス変更</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" {{ $job->status === 'active' ? 'selected' : '' }}>アクティブ</option>
                                        <option value="inactive" {{ $job->status === 'inactive' ? 'selected' : '' }}>非アクティブ</option>
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

