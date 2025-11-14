@extends('layouts.app')

@section('title', '店舗詳細 - システム管理者')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-store me-2"></i>店舗詳細
                </h1>
                <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>店舗一覧に戻る
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $shop->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $shop->id }}</td>
                                </tr>
                                <tr>
                                    <th>店舗名</th>
                                    <td>{{ $shop->name }}</td>
                                </tr>
                                <tr>
                                    <th>住所</th>
                                    <td>{{ $shop->prefecture->name ?? '' }}{{ $shop->city->name ?? '' }}{{ $shop->address }}</td>
                                </tr>
                                <tr>
                                    <th>電話番号</th>
                                    <td>{{ $shop->phone ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>メール</th>
                                    <td>{{ $shop->email ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ステータス</th>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $shop->status === 'active' ? 'success' : 
                                            ($shop->status === 'pending' || $shop->status === 'verification_pending' ? 'warning' : 'danger') 
                                        }}">
                                            {{ $shop->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.shops.update-status', $shop->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">ステータス変更</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" {{ $shop->status === 'active' ? 'selected' : '' }}>アクティブ</option>
                                        <option value="pending" {{ $shop->status === 'pending' ? 'selected' : '' }}>承認待ち</option>
                                        <option value="verification_pending" {{ $shop->status === 'verification_pending' ? 'selected' : '' }}>確認待ち</option>
                                        <option value="inactive" {{ $shop->status === 'inactive' ? 'selected' : '' }}>非アクティブ</option>
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

