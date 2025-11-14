@extends('layouts.app')

@section('title', 'サンプルデータ投入')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-database me-2"></i>サンプルデータ投入
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>サンプルデータを投入
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">テスト用のサンプルデータ（店舗、求人、キャストなど）を投入します。</p>
                    <form method="POST" action="{{ route('admin.sample-data.store') }}">
                        @csrf
                        <input type="hidden" name="action" value="insert_sample_data">
                        <button type="submit" class="btn btn-primary" 
                                onclick="return confirm('サンプルデータを投入しますか？既存のデータと重複する可能性があります。')">
                            <i class="fas fa-database me-2"></i>サンプルデータを投入
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>全データを削除
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3 text-danger">
                        <strong>警告:</strong> すべてのデータ（店舗、求人、応募、チャットなど）を削除します。この操作は取り消せません。
                    </p>
                    <form method="POST" action="{{ route('admin.sample-data.store') }}">
                        @csrf
                        <input type="hidden" name="action" value="clear_all_data">
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('本当にすべてのデータを削除しますか？この操作は取り消せません。')">
                            <i class="fas fa-trash me-2"></i>全データを削除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

