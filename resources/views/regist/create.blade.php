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
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>ご注意ください</strong><br>
            掲載審査を厳格に行っております。正確な情報をご入力ください。虚偽の情報が発覚した場合、掲載を停止する場合があります。
        </div>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>入力内容に誤りがあります。以下の項目をご確認ください。</strong>
                <ul class="mb-0 mt-2">
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

        <form method="POST" action="{{ route('regist.store') }}" class="needs-validation" novalidate id="registForm">
            @csrf
            
            <!-- 店舗基本情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-store me-2"></i>店舗基本情報
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">店舗名 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="100">
                    <small class="form-text text-muted">既に登録されている店舗名は使用できません</small>
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
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required minlength="20" maxlength="2000">{{ old('description') }}</textarea>
                <small class="form-text text-muted">20文字以上2000文字以内で入力してください</small>
                <div class="text-end">
                    <span id="description-count">0</span> / 2000文字
                </div>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="uniform_type" class="form-label">制服タイプ</label>
                    <input type="text" class="form-control @error('uniform_type') is-invalid @enderror" id="uniform_type" name="uniform_type" value="{{ old('uniform_type') }}" placeholder="メイド、執事など" maxlength="50">
                    @error('uniform_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="opening_hours" class="form-label">営業時間 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('opening_hours') is-invalid @enderror" id="opening_hours" name="opening_hours" value="{{ old('opening_hours') }}" placeholder="例：平日 12:00-22:00、土日祝 11:00-23:00" required maxlength="500">
                    @error('opening_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 住所情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-map-marker-alt me-2"></i>住所情報
            </h3>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="postal_code" class="form-label">郵便番号 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="123-4567" pattern="\d{3}-?\d{4}" required>
                    <small class="form-text text-muted">例：123-4567</small>
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
                    <input type="text" class="form-control @error('city_name') is-invalid @enderror" id="city_name" name="city_name" value="{{ old('city_name') }}" required maxlength="50">
                    @error('city_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">番地・建物名 <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required maxlength="200">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 連絡先情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-phone me-2"></i>連絡先情報
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">店舗電話番号 <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="03-1234-5678" pattern="0\d{1,4}-?\d{1,4}-?\d{4}" required maxlength="20">
                    <small class="form-text text-muted">例：03-1234-5678 または 090-1234-5678</small>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">店舗メールアドレス <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength="100">
                    <small class="form-text text-muted">既に登録されているメールアドレスは使用できません</small>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="website" class="form-label">ウェブサイトURL</label>
                <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com" maxlength="200">
                <small class="form-text text-muted">http://またはhttps://で始まるURLを入力してください</small>
                @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 事業形態 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-building me-2"></i>事業形態
            </h3>
            
            <div class="mb-3">
                <label class="form-label">事業形態 <span class="text-danger">*</span></label>
                <div>
                    <div class="form-check">
                        <input class="form-check-input @error('business_type') is-invalid @enderror" type="radio" name="business_type" id="business_type_individual" value="individual" {{ old('business_type') == 'individual' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="business_type_individual">
                            個人事業主
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('business_type') is-invalid @enderror" type="radio" name="business_type" id="business_type_corporation" value="corporation" {{ old('business_type') == 'corporation' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="business_type_corporation">
                            法人
                        </label>
                    </div>
                </div>
                @error('business_type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div id="corporation_fields" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="corporation_name" class="form-label">法人名 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('corporation_name') is-invalid @enderror" id="corporation_name" name="corporation_name" value="{{ old('corporation_name') }}" maxlength="200">
                        @error('corporation_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="corporation_number" class="form-label">法人番号</label>
                        <input type="text" class="form-control @error('corporation_number') is-invalid @enderror" id="corporation_number" name="corporation_number" value="{{ old('corporation_number') }}" maxlength="50">
                        @error('corporation_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 代表者情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-user-tie me-2"></i>代表者情報
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="representative_name" class="form-label">代表者名 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('representative_name') is-invalid @enderror" id="representative_name" name="representative_name" value="{{ old('representative_name') }}" required maxlength="100">
                    @error('representative_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="representative_position" class="form-label">役職 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('representative_position') is-invalid @enderror" id="representative_position" name="representative_position" value="{{ old('representative_position') }}" placeholder="例：代表取締役、オーナー" required maxlength="50">
                    @error('representative_position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 管理者情報 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-user-cog me-2"></i>管理者情報（管理画面ログイン用）
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_last_name" class="form-label">管理者姓 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('admin_last_name') is-invalid @enderror" id="admin_last_name" name="admin_last_name" value="{{ old('admin_last_name') }}" required maxlength="50">
                    <small class="form-text text-muted">全角カタカナまたは漢字で入力してください</small>
                    @error('admin_last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admin_first_name" class="form-label">管理者名 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('admin_first_name') is-invalid @enderror" id="admin_first_name" name="admin_first_name" value="{{ old('admin_first_name') }}" required maxlength="50">
                    <small class="form-text text-muted">全角カタカナまたは漢字で入力してください</small>
                    @error('admin_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_email" class="form-label">管理者メールアドレス <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required maxlength="255">
                    <small class="form-text text-muted">店舗メールアドレスとは異なるものを入力してください</small>
                    @error('admin_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admin_email_confirm" class="form-label">管理者メールアドレス（確認） <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('admin_email_confirm') is-invalid @enderror" id="admin_email_confirm" name="admin_email_confirm" value="{{ old('admin_email_confirm') }}" required maxlength="255">
                    @error('admin_email_confirm')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_phone" class="form-label">管理者電話番号 <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('admin_phone') is-invalid @enderror" id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}" placeholder="090-1234-5678" pattern="0\d{1,4}-?\d{1,4}-?\d{4}" required maxlength="20">
                    <small class="form-text text-muted">例：090-1234-5678</small>
                    @error('admin_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="admin_password" class="form-label">パスワード <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" required minlength="8" maxlength="255">
                    <small class="form-text text-muted">英大文字、英小文字、数字を含む8文字以上</small>
                    @error('admin_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admin_password_confirmation" class="form-label">パスワード（確認） <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" required minlength="8" maxlength="255">
                </div>
            </div>

            <!-- 利用規約・同意 -->
            <h3 class="h5 mb-3 mt-4" style="color: var(--cc-color-accent); border-bottom: 2px solid var(--cc-color-border); padding-bottom: 8px;">
                <i class="fas fa-file-contract me-2"></i>利用規約・同意事項
            </h3>
            
            <div class="cc-card mb-3" style="max-height: 300px; overflow-y: auto; background-color: #f8f9fa;">
                <h5 class="h6 mb-3">利用規約</h5>
                <div style="font-size: 12px; line-height: 1.6;">
                    <p><strong>第1条（適用）</strong></p>
                    <p>本規約は、カフェコレ（以下「当サイト」といいます）が提供するサービス（以下「本サービス」といいます）の利用条件を定めるものです。登録ユーザー（以下「ユーザー」といいます）は、本規約に従って本サービスを利用するものとします。</p>
                    
                    <p><strong>第2条（利用登録）</strong></p>
                    <p>本サービスの利用を希望する者は、本規約に同意のうえ、当サイトの定める方法により利用登録を申請し、当サイトが承認した時点で登録が完了します。</p>
                    
                    <p><strong>第3条（登録情報の管理）</strong></p>
                    <p>ユーザーは、自己の責任において登録情報を管理するものとします。ユーザーはいかなる場合も、登録情報を第三者に利用させたり、貸与・譲渡・名義変更・売買することはできません。</p>
                    
                    <p><strong>第4条（禁止事項）</strong></p>
                    <p>ユーザーは、本サービスの利用にあたり、以下の行為をしてはなりません。</p>
                    <ul>
                        <li>法令または公序良俗に違反する行為</li>
                        <li>犯罪行為に関連する行為</li>
                        <li>本サービスに含まれる著作権、商標権その他の知的財産権を侵害する行為</li>
                        <li>当サイト、他のユーザーまたは第三者のサーバー・ネットワーク機能を破壊または妨害する行為</li>
                        <li>当サイトの明示的な許可なく、本サービスによって得られた情報を商業的に利用する行為</li>
                        <li>当サイトの運営を妨害するおそれのある行為</li>
                        <li>不正アクセス行為またはこれに準ずる行為</li>
                        <li>他のユーザーに関する個人情報等を収集・蓄積する行為</li>
                        <li>不正な目的をもって本サービスを利用する行為</li>
                        <li>本サービスの他のユーザーまたは第三者に不利益、損害、不快感を与える行為</li>
                        <li>反社会的勢力（暴力団、暴力団員、暴力団準構成員、暴力団関係企業・団体、総会屋、社会運動等標榜ゴロ、特殊知能暴力集団等、その他これらに準ずる者）に該当すること、またはこれらに対して資金提供・便宜供与その他協力または関与する行為</li>
                        <li>その他、当サイトが不適切と判断する行為</li>
                    </ul>
                    
                    <p><strong>第5条（本サービスの提供の停止等）</strong></p>
                    <p>当サイトは、以下のいずれかの事由があると判断した場合、ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。</p>
                    <ul>
                        <li>本サービスにかかるコンピュータシステムの保守点検または更新を行う場合</li>
                        <li>地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合</li>
                        <li>コンピュータまたは通信回線等が事故により停止した場合</li>
                        <li>その他、当サイトが本サービスの提供が困難と判断した場合</li>
                    </ul>
                    
                    <p><strong>第6条（保証の否認および免責）</strong></p>
                    <p>当サイトは、本サービスに事実上または法律上の瑕疵がないことを保証しません。当サイトは、ユーザーに生じた損害について一切の責任を負わないものとします。また、ユーザー間またはユーザーと第三者との間で生じたトラブルについても責任を負いません。</p>
                    
                    <p><strong>第7条（サービス内容の変更等）</strong></p>
                    <p>当サイトは、ユーザーへの事前通知なく本サービスの内容を変更または提供を中止できるものとします。なお、重要な変更については、合理的な方法で通知します。</p>
                    
                    <p><strong>第8条（利用規約の変更）</strong></p>
                    <p>当サイトは、必要と判断した場合には、ユーザーに通知することなくいつでも本規約を変更することができるものとします。なお、本規約の変更後、本サービスの利用を開始した場合には、当該ユーザーは変更後の規約に同意したものとみなします。</p>
                    
                    <p><strong>第9条（個人情報の取扱い）</strong></p>
                    <p>当サイトは、本サービスの利用によって取得する個人情報については、当サイト「プライバシーポリシー」に従い適切に取り扱うものとします。</p>
                    
                    <p><strong>第10条（通知または連絡）</strong></p>
                    <p>ユーザーと当サイトとの間の通知または連絡は、当サイトの定める方法によって行うものとします。当サイトは、ユーザーから、当サイトが別途定める方式に従った変更届け出がない限り、現在登録されている連絡先が有効なものとみなして当該連絡先へ通知または連絡を行い、これらは、発信時にユーザーへ到達したものとみなします。</p>
                    
                    <p><strong>第11条（権利義務の譲渡の禁止）</strong></p>
                    <p>ユーザーは、当サイトの書面による事前の承諾なく、利用契約上の地位または本規約に基づく権利もしくは義務を第三者に譲渡し、または担保に供することはできません。</p>
                    
                    <p><strong>第12条（準拠法・裁判管轄）</strong></p>
                    <p>本規約の解釈にあたっては、日本法を準拠法とします。本サービスに関して紛争が生じた場合には、当サイトの本店所在地を管轄する裁判所を専属的合意管轄とします。</p>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" required>
                    <label class="form-check-label" for="terms">
                        利用規約に同意しました <span class="text-danger">*</span>
                    </label>
                    @error('terms')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 送信ボタン -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-paper-plane me-2"></i>送信する
                </button>
                <p class="mt-3 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    送信後、審査を行います。審査完了まで数営業日かかる場合があります。
                </p>
            </div>
        </form>
    </div>
</div>

<script>
// 文字数カウント
document.getElementById('description').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('description-count').textContent = count;
});

// 事業形態による表示切り替え
document.querySelectorAll('input[name="business_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const corporationFields = document.getElementById('corporation_fields');
        if (this.value === 'corporation') {
            corporationFields.style.display = 'block';
            document.getElementById('corporation_name').required = true;
            document.getElementById('corporation_number').required = false;
        } else {
            corporationFields.style.display = 'none';
            document.getElementById('corporation_name').required = false;
            document.getElementById('corporation_number').required = false;
        }
    });
});

// 初期状態の設定
if (document.getElementById('business_type_corporation').checked) {
    document.getElementById('corporation_fields').style.display = 'block';
    document.getElementById('corporation_name').required = true;
}

// 郵便番号の自動フォーマット
document.getElementById('postal_code').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 3) {
        value = value.slice(0, 3) + '-' + value.slice(3, 7);
    }
    e.target.value = value;
});

// 電話番号の自動フォーマット
['phone', 'admin_phone'].forEach(function(id) {
    document.getElementById(id).addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('0')) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length <= 10) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6);
            } else {
                value = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
            }
        }
        e.target.value = value;
    });
});
</script>
@endsection
