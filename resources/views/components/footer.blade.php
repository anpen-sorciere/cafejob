<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-coffee me-2"></i>カフェコレ（CafeColle）
                </h5>
                <p class="text-muted">
                    全国のコンカフェ・メンズコンカフェから「働きたい」「楽しみたい」お店のエリアから検索できるコンカフェ専門のポータルサイトです。
                </p>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">求職者の方</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('jobs.index') }}" class="text-muted text-decoration-none">求人検索</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">お店検索</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">キャスト検索</a></li>
                    <li><a href="{{ route('register') }}" class="text-muted text-decoration-none">新規登録</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">店舗様</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-muted text-decoration-none">店舗登録</a></li>
                    <li><a href="{{ route('shop-admin.login') }}" class="text-muted text-decoration-none">店舗ログイン</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">広告掲載</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">サポート</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-muted text-decoration-none">よくある質問</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">お問い合わせ</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">利用規約</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">プライバシーポリシー</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">管理者</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('admin.login') }}" class="text-muted text-decoration-none">システム管理者</a></li>
                </ul>
            </div>
        </div>
        
        <hr class="bg-secondary">
        
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="text-muted mb-0">&copy; {{ date('Y') }} カフェコレ（CafeColle）. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

