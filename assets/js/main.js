// カフェJob メインJavaScript

// ソート機能
function sortResults(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    url.searchParams.delete('p'); // ページ番号をリセット
    window.location.href = url.toString();
}

// お気に入り機能
document.addEventListener('DOMContentLoaded', function() {
    // お気に入りボタンのイベントリスナー
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const itemType = this.dataset.itemType;
            
            // ログイン状態をチェック
            if (!isLoggedIn()) {
                alert('お気に入り機能を使用するにはログインが必要です。');
                return;
            }
            
            toggleFavorite(itemId, itemType, this);
        });
    });
});

// お気に入り切り替え
function toggleFavorite(itemId, itemType, buttonElement) {
    const icon = buttonElement.querySelector('i');
    const isFavorited = icon.classList.contains('fas');
    
    fetch('api/favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            item_id: itemId,
            item_type: itemType,
            action: isFavorited ? 'remove' : 'add'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isFavorited) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                buttonElement.classList.remove('btn-danger');
                buttonElement.classList.add('btn-outline-danger');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                buttonElement.classList.remove('btn-outline-danger');
                buttonElement.classList.add('btn-danger');
            }
        } else {
            alert(data.message || 'エラーが発生しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    });
}

// ログイン状態チェック
function isLoggedIn() {
    // セッション情報をチェック（簡易版）
    return document.querySelector('.navbar-nav .dropdown') !== null;
}

// 検索フォームの自動送信
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('select, input[type="text"]');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // デバウンス処理
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            });
        });
    }
});

// レスポンシブ対応
function handleResize() {
    const navbar = document.querySelector('.navbar-collapse');
    if (window.innerWidth < 992) {
        navbar.classList.remove('show');
    }
}

window.addEventListener('resize', handleResize);

// スムーススクロール
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// アラートの自動非表示
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// 画像の遅延読み込み
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

document.addEventListener('DOMContentLoaded', lazyLoadImages);
