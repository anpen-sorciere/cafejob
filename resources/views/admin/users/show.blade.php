@extends('layouts.app')

@section('title', 'ユーザー詳細 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>ユーザー詳細
                </h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>ユーザー一覧に戻る
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $user->username }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>ユーザー名</th>
                                    <td>{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <th>氏名</th>
                                    <td>{{ $user->last_name }} {{ $user->first_name }}</td>
                                </tr>
                                <tr>
                                    <th>メールアドレス</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>電話番号</th>
                                    <td>{{ $user->phone ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ステータス</th>
                                    <td>
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.users.update-status', $user->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">ステータス変更</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>アクティブ</option>
                                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>非アクティブ</option>
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

