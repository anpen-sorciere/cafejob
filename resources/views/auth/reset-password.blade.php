@extends('layouts.auth')

@section('title', 'パスワード再設定')
@section('description', '新しいパスワードを設定してください。')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 auth-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-3">
                            <i class="fas fa-lock me-2 text-primary"></i>パスワード再設定
                        </h2>
                        <p class="text-muted">新しいパスワードを設定してください</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-1"></i>メールアドレス <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $request->email) }}" 
                                   required 
                                   autofocus 
                                   autocomplete="email"
                                   placeholder="メールアドレスを入力">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock me-1"></i>新しいパスワード <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="8文字以上のパスワード">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">8文字以上のパスワードを入力してください</small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">
                                <i class="fas fa-lock me-1"></i>パスワード（確認） <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="パスワードを再入力">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-4 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>パスワードを再設定
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>ログインページに戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
