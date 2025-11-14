@extends('layouts.shop-admin')

@section('title', 'キャスト管理')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>キャスト管理
                </h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCastModal">
                    <i class="fas fa-plus me-2"></i>新しいキャストを追加
                </button>
            </div>
        </div>
    </div>

    @if($casts->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">まだキャストが登録されていません</h4>
                <p class="text-muted">店舗で働くスタッフの情報を登録して、求人に活用しましょう。</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCastModal">
                    <i class="fas fa-plus me-2"></i>最初のキャストを追加
                </button>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($casts as $cast)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $cast->name }}</h6>
                            <span class="badge bg-{{ $cast->status === 'active' ? 'success' : 'warning' }}">
                                {{ $cast->status === 'active' ? '在籍中' : '非アクティブ' }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if($cast->profile_image)
                                <div class="text-center mb-3">
                                    <img src="{{ $cast->profile_image }}" 
                                         alt="{{ $cast->name }}" 
                                         class="img-fluid rounded" 
                                         style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">年齢</small>
                                    <div class="fw-bold">{{ $cast->age ? $cast->age . '歳' : '未設定' }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">登録日</small>
                                    <div class="fw-bold">{{ $cast->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d') }}</div>
                                </div>
                            </div>
                            @if($cast->nickname)
                                <div class="mb-2">
                                    <small class="text-muted">ニックネーム</small>
                                    <div>{{ $cast->nickname }}</div>
                                </div>
                            @endif
                            @if($cast->special_skill)
                                <div class="mb-2">
                                    <small class="text-muted">特技・専門</small>
                                    <div class="small">{{ $cast->special_skill }}</div>
                                </div>
                            @endif
                            @if($cast->hobby)
                                <p class="card-text text-muted small">
                                    {{ Str::limit($cast->hobby, 80) }}
                                </p>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#editCastModal{{ $cast->id }}">
                                    <i class="fas fa-edit me-1"></i>編集
                                </button>
                                <form method="POST" action="{{ route('shop-admin.cast-management.destroy', $cast->id) }}" 
                                      class="d-inline" onsubmit="return confirm('このキャストを削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i>削除
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 編集モーダル -->
                <div class="modal fade" id="editCastModal{{ $cast->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">キャスト情報を編集</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('shop-admin.cast-management.update', $cast->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="name{{ $cast->id }}" class="form-label">名前 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name{{ $cast->id }}" name="name" 
                                               value="{{ $cast->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nickname{{ $cast->id }}" class="form-label">ニックネーム</label>
                                        <input type="text" class="form-control" id="nickname{{ $cast->id }}" name="nickname" 
                                               value="{{ $cast->nickname }}">
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="age{{ $cast->id }}" class="form-label">年齢</label>
                                            <input type="number" class="form-control" id="age{{ $cast->id }}" name="age" 
                                                   min="18" max="50" value="{{ $cast->age }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="height{{ $cast->id }}" class="form-label">身長</label>
                                            <input type="number" class="form-control" id="height{{ $cast->id }}" name="height" 
                                                   min="140" max="200" value="{{ $cast->height }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="blood_type{{ $cast->id }}" class="form-label">血液型</label>
                                        <select class="form-select" id="blood_type{{ $cast->id }}" name="blood_type">
                                            <option value="">選択してください</option>
                                            <option value="A" {{ $cast->blood_type === 'A' ? 'selected' : '' }}>A型</option>
                                            <option value="B" {{ $cast->blood_type === 'B' ? 'selected' : '' }}>B型</option>
                                            <option value="O" {{ $cast->blood_type === 'O' ? 'selected' : '' }}>O型</option>
                                            <option value="AB" {{ $cast->blood_type === 'AB' ? 'selected' : '' }}>AB型</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="hobby{{ $cast->id }}" class="form-label">趣味</label>
                                        <textarea class="form-control" id="hobby{{ $cast->id }}" name="hobby" rows="2">{{ $cast->hobby }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="special_skill{{ $cast->id }}" class="form-label">特技・専門</label>
                                        <textarea class="form-control" id="special_skill{{ $cast->id }}" name="special_skill" rows="2">{{ $cast->special_skill }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status{{ $cast->id }}" class="form-label">ステータス</label>
                                        <select class="form-select" id="status{{ $cast->id }}" name="status">
                                            <option value="active" {{ $cast->status === 'active' ? 'selected' : '' }}>在籍中</option>
                                            <option value="inactive" {{ $cast->status === 'inactive' ? 'selected' : '' }}>非アクティブ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                    <button type="submit" class="btn btn-primary">更新</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- 追加モーダル -->
<div class="modal fade" id="addCastModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新しいキャストを追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('shop-admin.cast-management.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">名前 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nickname" class="form-label">ニックネーム</label>
                        <input type="text" class="form-control @error('nickname') is-invalid @enderror" id="nickname" name="nickname" 
                               value="{{ old('nickname') }}">
                        @error('nickname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="age" class="form-label">年齢</label>
                            <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" 
                                   min="18" max="50" value="{{ old('age') }}">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="height" class="form-label">身長</label>
                            <input type="number" class="form-control @error('height') is-invalid @enderror" id="height" name="height" 
                                   min="140" max="200" value="{{ old('height') }}">
                            @error('height')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="blood_type" class="form-label">血液型</label>
                        <select class="form-select @error('blood_type') is-invalid @enderror" id="blood_type" name="blood_type">
                            <option value="">選択してください</option>
                            <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A型</option>
                            <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B型</option>
                            <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O型</option>
                            <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB型</option>
                        </select>
                        @error('blood_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="hobby" class="form-label">趣味</label>
                        <textarea class="form-control @error('hobby') is-invalid @enderror" id="hobby" name="hobby" rows="2">{{ old('hobby') }}</textarea>
                        @error('hobby')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="special_skill" class="form-label">特技・専門</label>
                        <textarea class="form-control @error('special_skill') is-invalid @enderror" id="special_skill" name="special_skill" rows="2">{{ old('special_skill') }}</textarea>
                        @error('special_skill')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">ステータス</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>在籍中</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>非アクティブ</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

