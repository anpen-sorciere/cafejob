@extends('layouts.app')

@section('title', '店舗管理者アカウント作成')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-user-plus me-2"></i>店舗管理者アカウント作成
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>ダッシュボードに戻る
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">店舗管理者アカウント作成</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.shop-admins.store') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shop_id" class="form-label">店舗 <span class="text-danger">*</span></label>
                                <select class="form-select @error('shop_id') is-invalid @enderror" id="shop_id" name="shop_id" required>
                                    <option value="">店舗を選択してください</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('shop_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">ユーザー名 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" 
                                       value="{{ old('username') }}" 
                                       required
                                       pattern="[a-zA-Z0-9_]+"
                                       minlength="3"
                                       maxlength="50">
                                <div class="form-text">3文字以上の英数字とアンダースコア</div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">パスワード <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           required
                                           minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">6文字以上</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">パスワード確認 <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       required
                                       minlength="6">
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>店舗管理者アカウント作成
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">既存の店舗管理者</h6>
                </div>
                <div class="card-body">
                    @if($shopAdmins->isEmpty())
                        <p class="text-muted">店舗管理者アカウントがありません</p>
                    @else
                        @foreach($shopAdmins as $admin)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $admin->username }}</h6>
                                    <p class="mb-1 small text-muted">{{ $admin->shop->name }}</p>
                                    <small class="text-muted">{{ $admin->email }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-{{ $admin->status === 'active' ? 'success' : 'danger' }}">
                                        {{ $admin->status === 'active' ? '有効' : '無効' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// パスワード確認のリアルタイムバリデーション
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const passwordConfirm = this.value;
    
    if (passwordConfirm && password !== passwordConfirm) {
        this.setCustomValidity('パスワードが一致しません。');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endpush
@endsection

