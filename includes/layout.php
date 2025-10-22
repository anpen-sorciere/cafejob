<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'コンカフェ専門の求人・集客サイト。全国のコンカフェ・メンズコンカフェから働きたい・楽しみたいお店を検索できます。'; ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-coffee me-2"></i><?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>ホーム
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'jobs') ? 'active' : ''; ?>" href="?page=jobs">
                            <i class="fas fa-briefcase me-1"></i>求人検索
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'shops') ? 'active' : ''; ?>" href="?page=shops">
                            <i class="fas fa-store me-1"></i>お店検索
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'cast') ? 'active' : ''; ?>" href="?page=cast">
                            <i class="fas fa-users me-1"></i>キャスト
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'chat') ? 'active' : ''; ?>" href="?page=chat">
                            <i class="fas fa-comments me-1"></i>チャット
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'updates') ? 'active' : ''; ?>" href="?page=updates">
                            <i class="fas fa-clock me-1"></i>最新情報
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (is_logged_in() && isset($_SESSION['username'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?page=profile"><i class="fas fa-user-circle me-2"></i>プロフィール</a></li>
                                <li><a class="dropdown-item" href="?page=applications"><i class="fas fa-file-alt me-2"></i>応募履歴</a></li>
                                <li><a class="dropdown-item" href="?page=favorites"><i class="fas fa-heart me-2"></i>お気に入り</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?action=logout"><i class="fas fa-sign-out-alt me-2"></i>ログアウト</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=login">
                                <i class="fas fa-sign-in-alt me-1"></i>ログイン
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=register">
                                <i class="fas fa-user-plus me-1"></i>新規登録
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-coffee me-2"></i><?php echo SITE_NAME; ?>
                    </h5>
                    <p class="text-muted">
                        全国のコンカフェ・メンズコンカフェから「働きたい」「楽しみたい」お店のエリアから検索できるコンカフェ専門のポータルサイトです。
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">求職者の方</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=jobs" class="text-muted text-decoration-none">求人検索</a></li>
                        <li><a href="?page=shops" class="text-muted text-decoration-none">お店検索</a></li>
                        <li><a href="?page=cast" class="text-muted text-decoration-none">キャスト検索</a></li>
                        <li><a href="?page=register" class="text-muted text-decoration-none">新規登録</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">店舗様</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=shop_register" class="text-muted text-decoration-none">店舗登録</a></li>
                        <li><a href="?page=shop_login" class="text-muted text-decoration-none">店舗ログイン</a></li>
                        <li><a href="?page=advertisement" class="text-muted text-decoration-none">広告掲載</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">サポート</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=help" class="text-muted text-decoration-none">ヘルプ</a></li>
                        <li><a href="?page=contact" class="text-muted text-decoration-none">お問い合わせ</a></li>
                        <li><a href="?page=privacy" class="text-muted text-decoration-none">プライバシーポリシー</a></li>
                        <li><a href="?page=terms" class="text-muted text-decoration-none">利用規約</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">SNS</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        <i class="fas fa-heart text-danger me-1"></i>
                        Made with love for the cafe community
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
