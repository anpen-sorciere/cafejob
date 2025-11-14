@extends('layouts.shop-admin')

@section('title', '店舗情報編集')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-store me-2"></i>店舗情報編集
                    </h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('shop-admin.shop-info.update') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- 基本情報 -->
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>基本情報
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">店舗名 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   placeholder="例: カフェ・ドリーム"
                                   value="{{ old('name', $shop->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">店舗名を入力してください</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">店舗説明 <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required
                                      placeholder="店舗の特徴や魅力を詳しく記載してください">{{ old('description', $shop->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">店舗説明を入力してください</div>
                            @enderror
                        </div>

                        <!-- 住所情報 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-map-marker-alt me-2"></i>住所情報
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">郵便番号 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" name="postal_code" 
                                       placeholder="例: 100-0001" maxlength="8"
                                       value="{{ old('postal_code', $shop->postal_code ? substr($shop->postal_code, 0, 3) . '-' . substr($shop->postal_code, 3) : '') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">郵便番号を入力してください</div>
                                @enderror
                                <div class="form-text">郵便番号を入力すると住所が自動で補完されます</div>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary" id="search_address">
                                    <i class="fas fa-search me-1"></i>住所を検索
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="prefecture_id" class="form-label">都道府県 <span class="text-danger">*</span></label>
                                <select class="form-select @error('prefecture_id') is-invalid @enderror" 
                                        id="prefecture_id" name="prefecture_id" required>
                                    <option value="">選択してください</option>
                                    @foreach($prefectures as $pref)
                                        <option value="{{ $pref->id }}" 
                                            {{ old('prefecture_id', $shop->prefecture_id) == $pref->id ? 'selected' : '' }}>
                                            {{ $pref->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prefecture_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">都道府県を選択してください</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="city_id" class="form-label">市区町村</label>
                                <select class="form-select @error('city_id') is-invalid @enderror" 
                                        id="city_id" name="city_id">
                                    <option value="">選択してください</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" 
                                            {{ old('city_id', $shop->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">住所 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" 
                                   placeholder="例: なんば1-2-3 なんばビル4F"
                                   value="{{ old('address', $shop->address) }}" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">住所を入力してください</div>
                            @enderror
                        </div>

                        <!-- 連絡先情報 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-phone me-2"></i>連絡先情報
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">電話番号 <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       placeholder="例: 06-1234-5678"
                                       value="{{ old('phone', $shop->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">電話番号を入力してください</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       placeholder="例: info@example.com"
                                       value="{{ old('email', $shop->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">メールアドレスを入力してください</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">ウェブサイト</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" 
                                   placeholder="例: https://example.com"
                                   value="{{ old('website', $shop->website) }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 営業情報 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-clock me-2"></i>営業情報
                        </h5>

                        <div class="mb-3">
                            <label for="opening_hours" class="form-label">営業時間 <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('opening_hours') is-invalid @enderror" 
                                      id="opening_hours" name="opening_hours" rows="3" required
                                      placeholder="例: 平日 10:00-22:00&#10;土日祝 9:00-23:00">{{ old('opening_hours', $shop->opening_hours) }}</textarea>
                            @error('opening_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">営業時間を入力してください</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="concept_type" class="form-label">コンセプト <span class="text-danger">*</span></label>
                                <select class="form-select @error('concept_type') is-invalid @enderror" 
                                        id="concept_type" name="concept_type" required>
                                    <option value="other" {{ old('concept_type', $shop->concept_type) === 'other' ? 'selected' : '' }}>その他</option>
                                    <option value="maid" {{ old('concept_type', $shop->concept_type) === 'maid' ? 'selected' : '' }}>メイド</option>
                                    <option value="butler" {{ old('concept_type', $shop->concept_type) === 'butler' ? 'selected' : '' }}>バトラー</option>
                                    <option value="idol" {{ old('concept_type', $shop->concept_type) === 'idol' ? 'selected' : '' }}>アイドル</option>
                                    <option value="cosplay" {{ old('concept_type', $shop->concept_type) === 'cosplay' ? 'selected' : '' }}>コスプレ</option>
                                </select>
                                @error('concept_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">コンセプトを選択してください</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="uniform_type" class="form-label">制服タイプ</label>
                                <input type="text" class="form-control @error('uniform_type') is-invalid @enderror" 
                                       id="uniform_type" name="uniform_type" 
                                       placeholder="例: メイド服、制服など"
                                       value="{{ old('uniform_type', $shop->uniform_type) }}">
                                @error('uniform_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- 画像 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-image me-2"></i>店舗画像
                        </h5>

                        <div class="mb-3">
                            <label for="image" class="form-label">店舗画像</label>
                            @if($shop->image_url)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($shop->image_url) }}" alt="店舗画像" 
                                         class="img-thumbnail" style="max-width: 300px; max-height: 300px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">JPG, PNG, GIF形式（最大5MB）</small>
                        </div>

                        <!-- お店の雰囲気画像 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-images me-2"></i>お店の雰囲気画像
                        </h5>
                        <p class="text-muted small mb-3">お店の雰囲気が伝わる画像を追加してください</p>

                        @php
                            $currentAtmosphereImages = old('atmosphere_images', $shop->atmosphere_images ?? []);
                            if (is_string($currentAtmosphereImages)) {
                                $currentAtmosphereImages = json_decode($currentAtmosphereImages, true) ?? [];
                            }
                            if (!is_array($currentAtmosphereImages)) {
                                $currentAtmosphereImages = [];
                            }
                        @endphp

                        <div class="mb-3">
                            <label for="atmosphere_images" class="form-label">雰囲気画像URL</label>
                            <div id="atmosphere-images-container">
                                @if(!empty($currentAtmosphereImages))
                                    @foreach($currentAtmosphereImages as $index => $imageUrl)
                                        <div class="input-group mb-2">
                                            <input type="url" class="form-control @error('atmosphere_images.' . $index) is-invalid @enderror" 
                                                   name="atmosphere_images[]" 
                                                   placeholder="https://example.com/atmosphere{{ $index + 1 }}.jpg"
                                                   value="{{ old('atmosphere_images.' . $index, $imageUrl) }}">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeAtmosphereImage(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="url" class="form-control @error('atmosphere_images.0') is-invalid @enderror" 
                                               name="atmosphere_images[]" 
                                               placeholder="https://example.com/atmosphere1.jpg"
                                               value="{{ old('atmosphere_images.0') }}">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeAtmosphereImage(this)" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addAtmosphereImage()">
                                <i class="fas fa-plus me-1"></i>画像URLを追加
                            </button>
                            <small class="form-text text-muted d-block mt-2">お店の雰囲気が伝わる画像URLを追加できます（最大10枚）</small>
                            @error('atmosphere_images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- アルバイトの特徴 -->
                        <h5 class="mb-3 mt-4">
                            <i class="fas fa-star me-2"></i>アルバイトの特徴
                        </h5>
                        <p class="text-muted small mb-3">この店舗で働くアルバイトの特徴や魅力を記載してください</p>

                        <div class="mb-3">
                            <label for="job_features" class="form-label">アルバイトの特徴</label>
                            <textarea class="form-control @error('job_features') is-invalid @enderror" 
                                      id="job_features" name="job_features" rows="6"
                                      placeholder="例: 明るく元気なスタッフが多く、チームワークが良い職場です。接客スキルを身につけながら、楽しく働けます。">{{ old('job_features', $shop->job_features) }}</textarea>
                            <small class="form-text text-muted">この店舗で働くアルバイトの特徴、職場の雰囲気、働きやすさなどを詳しく記載してください</small>
                            @error('job_features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-info btn-lg">
                                <i class="fas fa-save me-2"></i>店舗情報を更新
                            </button>
                            <a href="{{ route('shop-admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>ダッシュボードに戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let atmosphereImageCount = {{ count($currentAtmosphereImages) > 0 ? count($currentAtmosphereImages) : 1 }};
const maxAtmosphereImages = 10;

function addAtmosphereImage() {
    if (atmosphereImageCount >= maxAtmosphereImages) {
        alert('画像は最大' + maxAtmosphereImages + '枚まで追加できます');
        return;
    }
    
    const container = document.getElementById('atmosphere-images-container');
    const newInput = document.createElement('div');
    newInput.className = 'input-group mb-2';
    newInput.innerHTML = `
        <input type="url" class="form-control" 
               name="atmosphere_images[]" 
               placeholder="https://example.com/atmosphere${atmosphereImageCount + 1}.jpg">
        <button type="button" class="btn btn-outline-danger" onclick="removeAtmosphereImage(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(newInput);
    atmosphereImageCount++;
    updateAtmosphereRemoveButtons();
}

function removeAtmosphereImage(button) {
    button.closest('.input-group').remove();
    atmosphereImageCount--;
    updateAtmosphereRemoveButtons();
}

function updateAtmosphereRemoveButtons() {
    const buttons = document.querySelectorAll('#atmosphere-images-container .btn-outline-danger');
    buttons.forEach(btn => {
        btn.style.display = atmosphereImageCount > 1 ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateAtmosphereRemoveButtons();
    const postalCodeInput = document.getElementById('postal_code');
    const searchButton = document.getElementById('search_address');
    const prefectureSelect = document.getElementById('prefecture_id');
    const citySelect = document.getElementById('city_id');
    const addressInput = document.getElementById('address');

    // 都道府県変更時に市区町村を更新
    prefectureSelect.addEventListener('change', function() {
        const prefectureId = this.value;
        if (prefectureId) {
            fetch(`/api/cities?prefecture_id=${prefectureId}`)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">選択してください</option>';
                    data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            citySelect.innerHTML = '<option value="">選択してください</option>';
        }
    });

    // 郵便番号の自動補完機能
    searchButton.addEventListener('click', function() {
        const postalCode = postalCodeInput.value.replace(/[^0-9]/g, '');
        
        if (postalCode.length !== 7) {
            alert('郵便番号は7桁で入力してください');
            return;
        }

        fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postalCode}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.results && data.results.length > 0) {
                    const result = data.results[0];
                    
                    // 都道府県を設定
                    const prefectureName = result.address1;
                    for (let option of prefectureSelect.options) {
                        if (option.text.includes(prefectureName)) {
                            option.selected = true;
                            option.dispatchEvent(new Event('change'));
                            break;
                        }
                    }
                    
                    // 住所フィールドに町域を設定
                    addressInput.value = result.address3;
                    
                    // 成功メッセージ
                    alert('住所を自動補完しました。番地・建物名を追加してください。');
                } else {
                    alert('郵便番号が見つかりませんでした。');
                }
            })
            .catch(error => {
                alert('住所検索中にエラーが発生しました。');
            });
    });

    // Bootstrapバリデーション
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endpush
@endsection

