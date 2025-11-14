<form method="post" action="{{ route('profile.update-images') }}" enctype="multipart/form-data" class="mt-3">
    @csrf
    @method('patch')

    <div class="row">
        <!-- プロフィール写真1 -->
        <div class="col-md-6 mb-4">
            <label class="form-label">プロフィール写真1</label>
            <div class="mb-3">
                @if($user->profile_image_1)
                    <div class="mb-2">
                        <img src="{{ Storage::url($user->profile_image_1) }}" alt="プロフィール写真1" 
                             class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                    </div>
                    <button type="submit" name="delete_profile_image_1" value="1" 
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('この写真を削除しますか？')">
                        <i class="fas fa-trash me-1"></i>削除
                    </button>
                @else
                    <div class="border rounded p-3 text-center text-muted" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                        <div>
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">写真がありません</p>
                        </div>
                    </div>
                @endif
            </div>
            <input type="file" class="form-control @error('profile_image_1') is-invalid @enderror" 
                   id="profile_image_1" name="profile_image_1" accept="image/jpeg,image/png,image/jpg,image/gif">
            @error('profile_image_1')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">JPG, PNG, GIF形式（最大5MB）</small>
        </div>

        <!-- プロフィール写真2 -->
        <div class="col-md-6 mb-4">
            <label class="form-label">プロフィール写真2</label>
            <div class="mb-3">
                @if($user->profile_image_2)
                    <div class="mb-2">
                        <img src="{{ Storage::url($user->profile_image_2) }}" alt="プロフィール写真2" 
                             class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                    </div>
                    <button type="submit" name="delete_profile_image_2" value="1" 
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('この写真を削除しますか？')">
                        <i class="fas fa-trash me-1"></i>削除
                    </button>
                @else
                    <div class="border rounded p-3 text-center text-muted" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                        <div>
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">写真がありません</p>
                        </div>
                    </div>
                @endif
            </div>
            <input type="file" class="form-control @error('profile_image_2') is-invalid @enderror" 
                   id="profile_image_2" name="profile_image_2" accept="image/jpeg,image/png,image/jpg,image/gif">
            @error('profile_image_2')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">JPG, PNG, GIF形式（最大5MB）</small>
        </div>
    </div>

            @if (session('status') === 'profile-images-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    プロフィール写真を更新しました。
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>写真を保存
        </button>
    </div>
</form>

