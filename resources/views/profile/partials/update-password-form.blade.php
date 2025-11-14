<form method="post" action="{{ route('password.update') }}" class="mt-3">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="current_password" class="form-label">現在のパスワード</label>
        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
               id="current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">新しいパスワード</label>
        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
               id="password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">新しいパスワード（確認）</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
               autocomplete="new-password">
    </div>

    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            パスワードを更新しました。
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>保存
        </button>
    </div>
</form>
