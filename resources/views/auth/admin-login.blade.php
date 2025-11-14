@extends('layouts.auth')

@section('title', 'システム管理者ログイン')
@section('description', 'cafejob(仮)システム管理者パネルにログインしてください。')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 auth-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-3">
                            <i class="fas fa-user-shield me-2 text-danger"></i>cafejob(仮)システム管理者ログイン
                        </h2>
                        <p class="text-muted">システム管理者パネルにログインしてください</p>
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

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>ユーザー名
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus
                                   placeholder="ユーザー名を入力">
                            @error('username')
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
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </button>
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
