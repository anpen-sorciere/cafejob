@extends('layouts.auth')

@section('title', 'ログイン')
@section('description', 'カフェJobにログインして求人情報を確認・応募できます。')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 auth-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-3">
                            <i class="fas fa-sign-in-alt me-2 text-primary"></i>ログイン
                        </h2>
                        <p class="text-muted">カフェJobにログインして求人情報を確認・応募できます</p>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Username or Email -->
                        <div class="mb-3">
                            <label for="login" class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>ユーザー名またはメールアドレス
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('login') is-invalid @enderror" 
                                   id="login" 
                                   name="login" 
                                   value="{{ old('login') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="ユーザー名またはメールアドレスを入力">
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock me-1"></i>パスワード
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="パスワードを入力">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    ログイン状態を保持する
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mb-3">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">
                                    <i class="fas fa-key me-1"></i>パスワードを忘れた方はこちら
                                </a>
                            </div>
                        @endif

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-2">アカウントをお持ちでない方は</p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>新規登録
                            </a>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>トップページに戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
