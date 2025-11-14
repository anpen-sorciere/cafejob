<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-3">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="username" class="form-label">ユーザー名 <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" 
                   value="{{ old('username', $user->username) }}" required autofocus>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">名</label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" 
                       value="{{ old('first_name', $user->first_name) }}">
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">姓</label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" 
                       value="{{ old('last_name', $user->last_name) }}">
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">生年月日</label>
            <div class="form-control-plaintext">
                @if($user->birth_date)
                    {{ \Carbon\Carbon::parse($user->birth_date)->format('Y年m月d日') }}
                    <span class="text-muted">({{ \Carbon\Carbon::parse($user->birth_date)->age }}歳)</span>
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-lock me-1"></i>生年月日は登録後に変更できません
                    </small>
                @else
                    <span class="text-muted">未登録</span>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                   value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    プロフィール情報を更新しました。
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>保存
            </button>
        </div>
    </form>
