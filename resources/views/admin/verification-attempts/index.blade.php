@extends('layouts.app')

@section('title', '確認コード入力履歴')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-key me-2"></i>確認コード入力履歴
                </h1>
                <div class="text-muted small">
                    総件数: {{ number_format($attempts->total()) }}件
                </div>
            </div>
        </div>
    </div>

    <!-- フィルター -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="shop_id" class="form-label">店舗</label>
                    <select class="form-select" id="shop_id" name="shop_id">
                        <option value="">すべて</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="attempt_type" class="form-label">タイプ</label>
                    <select class="form-select" id="attempt_type" name="attempt_type">
                        <option value="">すべて</option>
                        <option value="initial_registration" {{ request('attempt_type') === 'initial_registration' ? 'selected' : '' }}>初期登録</option>
                        <option value="address_change" {{ request('attempt_type') === 'address_change' ? 'selected' : '' }}>住所変更</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="is_successful" class="form-label">結果</label>
                    <select class="form-select" id="is_successful" name="is_successful">
                        <option value="">すべて</option>
                        <option value="1" {{ request('is_successful') === '1' ? 'selected' : '' }}>成功</option>
                        <option value="0" {{ request('is_successful') === '0' ? 'selected' : '' }}>失敗</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>検索
                    </button>
                    <a href="{{ route('admin.verification-attempts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>クリア
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 履歴一覧 -->
    <div class="card">
        <div class="card-body p-0">
            @if($attempts->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">入力履歴がありません</h5>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>日時</th>
                                <th>店舗</th>
                                <th>タイプ</th>
                                <th>正しいコード</th>
                                <th>入力コード</th>
                                <th>結果</th>
                                <th>IPアドレス</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td>
                                        <small>{{ $attempt->attempt_time->setTimezone('Asia/Tokyo')->format('Y/m/d H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $attempt->shop->name }}</strong>
                                            <br><small class="text-muted">{{ $attempt->shop->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attempt->attempt_type === 'initial_registration' ? 'primary' : 'info' }}">
                                            {{ $attempt->attempt_type === 'initial_registration' ? '初期登録' : '住所変更' }}
                                        </span>
                                    </td>
                                    <td>
                                        <code>{{ $attempt->verification_code }}</code>
                                    </td>
                                    <td>
                                        <code class="{{ $attempt->is_successful ? 'text-success' : 'text-danger' }}">
                                            {{ $attempt->input_code }}
                                        </code>
                                    </td>
                                    <td>
                                        @if($attempt->is_successful)
                                            <span class="badge bg-success">成功</span>
                                        @else
                                            <span class="badge bg-danger">失敗</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $attempt->ip_address }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- ページネーション -->
                <div class="mt-4 px-3">
                    {{ $attempts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

