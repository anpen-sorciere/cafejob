<?php
$page_title = 'ホーム';
$page_description = 'コンカフェ専門の求人・集客サイト';

ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-4"><?php echo SITE_NAME; ?></h1>
            <p class="lead mb-4">
                全国のコンカフェ・メンズコンカフェから「働きたい」「楽しみたい」<br>
                お店のエリアから検索できるコンカフェ専門のポータルサイトです。
            </p>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap mb-5">
                <a href="?page=jobs" class="btn btn-primary btn-lg">
                    <i class="fas fa-briefcase me-2"></i>求人を探す
                </a>
                <a href="?page=shops" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-store me-2"></i>お店を探す
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-search fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">簡単検索</h5>
                            <p class="card-text">エリアやコンセプトから理想のお店を簡単に検索できます。</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">豊富な情報</h5>
                            <p class="card-text">求人情報からキャスト情報まで、豊富な情報を提供しています。</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">お気に入り機能</h5>
                            <p class="card-text">気になった求人やお店をお気に入りに保存できます。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>