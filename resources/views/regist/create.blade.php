@extends('layouts.app')

@section('title', '求人掲載申し込み - カフェコレ（CafeColle）')
@section('description', 'コンカフェ専門の求人・集客サイト「カフェコレ」への求人掲載申し込みページです。5分でカンタン申し込み♪無料で掲載できます。')

@section('content')
<!-- ヒーローセクション -->
<section class="cc-hero-section mb-4">
    <div class="cc-container position-relative" style="z-index: 1;">
        <div class="cc-card text-center">
            <h1 class="h2 mb-3 fw-bold">コンカフェ専門求人サイト<span style="color: var(--cc-color-accent);"> カフェコレ</span></h1>
            <p class="h5 mb-4">5分でカンタン申し込み♪ 無料で掲載する</p>
            <p class="mb-0">365日フルサポート！</p>
        </div>
    </div>
</section>

<!-- 申し込みフォーム -->
<div class="cc-container mb-4">
    <div class="cc-card">
        <h2 class="h4 mb-4 text-center">求人掲載申し込みフォーム</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('regist.store') }}" class="needs-validation" novalidate>
            @csrf
            
            <!-- 店舗基本情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">店舗基本情報</h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">店舗名 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="concept_type" class="form-label">コンセプト <span class="text-danger">*</span></label>
                    <select class="form-select @error('concept_type') is-invalid @enderror" id="concept_type" name="concept_type" required>
                        <option value="">選択してください</option>
                        <option value="maid" {{ old('concept_type') == 'maid' ? 'selected' : '' }}>メイドカフェ</option>
                        <option value="butler" {{ old('concept_type') == 'butler' ? 'selected' : '' }}>執事喫茶</option>
                        <option value="idol" {{ old('concept_type') == 'idol' ? 'selected' : '' }}>アイドルカフェ</option>
                        <option value="cosplay" {{ old('concept_type') == 'cosplay' ? 'selected' : '' }}>コスプレカフェ</option>
                        <option value="other" {{ old('concept_type') == 'other' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('concept_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">店舗説明 <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 住所情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">住所情報</h3>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="postal_code" class="form-label">郵便番号 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="123-4567" required>
                    @error('postal_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="prefecture_id" class="form-label">都道府県 <span class="text-danger">*</span></label>
                    <select class="form-select @error('prefecture_id') is-invalid @enderror" id="prefecture_id" name="prefecture_id" required>
                        <option value="">選択してください</option>
                        @foreach($prefectures as $prefecture)
                            <option value="{{ $prefecture->id }}" {{ old('prefecture_id') == $prefecture->id ? 'selected' : '' }}>
                                {{ $prefecture->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('prefecture_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="city_name" class="form-label">市区町村 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('city_name') is-invalid @enderror" id="city_name" name="city_name" value="{{ old('city_name') }}" required>
                    @error('city_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">番地・建物名 <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 連絡先情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">連絡先情報</h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">電話番号 <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="website" class="form-label">ウェブサイトURL</label>
                    <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="uniform_type" class="form-label">制服タイプ</label>
                    <input type="text" class="form-control @error('uniform_type') is-invalid @enderror" id="uniform_type" name="uniform_type" value="{{ old('uniform_type') }}" placeholder="メイド、執事など">
                    @error('uniform_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="opening_hours" class="form-label">営業時間</label>
                <textarea class="form-control @error('opening_hours') is-invalid @enderror" id="opening_hours" name="opening_hours" rows="2">{{ old('opening_hours') }}</textarea>
                @error('opening_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 管理者情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">管理者情報</h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_last_name" class="form-label">管理者姓 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('admin_last_name') is-invalid @enderror" id="admin_last_name" name="admin_last_name" value="{{ old('admin_last_name') }}" required>
                    @error('admin_last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admin_first_name" class="form-label">管理者名 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('admin_first_name') is-invalid @enderror" id="admin_first_name" name="admin_first_name" value="{{ old('admin_first_name') }}" required>
                    @error('admin_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_email" class="form-label">管理者メールアドレス <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                    @error('admin_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admin_email_confirm" class="form-label">管理者メールアドレス（確認） <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('admin_email_confirm') is-invalid @enderror" id="admin_email_confirm" name="admin_email_confirm" value="{{ old('admin_email_confirm') }}" required>
                    @error('admin_email_confirm')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_password" class="form-label">パスワード <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" required>
                    <small class="form-text text-muted">8文字以上</small>
                    @error('admin_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admin_password_confirmation" class="form-label">パスワード（確認） <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" required>
                </div>
            </div>

            <!-- 利用規約 -->
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" required>
                    <label class="form-check-label" for="terms">
                        利用規約に同意しました <span class="text-danger">*</span>
                    </label>
                    @error('terms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 送信ボタン -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-paper-plane me-2"></i>送信する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

