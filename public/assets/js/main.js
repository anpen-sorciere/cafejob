// カフェJob メインJavaScript

// ソート機能
function sortResults(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    url.searchParams.delete('p'); // ページ番号をリセット
    window.location.href = url.toString();
}

// --- キープ機能 --------------------------------------------------------

function cjIsLoggedIn() {
    return document.body.dataset.cjLoggedIn === '1';
}

function isLoggedIn() {
    // 既存コードとの互換性を維持
    return cjIsLoggedIn();
}

function cjRequireLoginModal() {
    const modalElement = document.getElementById('cj-login-modal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    } else {
        window.location.href = '?page=login';
    }
}

function cjShowToast(message, variant = 'info') {
    const container = document.getElementById('cj-toast-container');
    if (!container) {
        alert(message);
        return;
    }

    const toastEl = document.createElement('div');
    const variantClass = `text-bg-${variant}`;

    toastEl.className = `toast align-items-center ${variantClass} border-0 cj-toast`;
    toastEl.setAttribute('role', 'status');
    toastEl.setAttribute('aria-live', 'polite');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="toast-body flex-grow-1">
                <i class="fas ${variant === 'success' ? 'fa-check-circle' : variant === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-3" data-bs-dismiss="toast" aria-label="閉じる" style="flex-shrink: 0;"></button>
        </div>
    `;

    container.appendChild(toastEl);

    if (typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastEl, { 
            delay: 4000,
            autohide: true,
            animation: true
        });
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        toast.show();
    } else {
        setTimeout(() => toastEl.remove(), 4000);
    }
}

function cjUpdateKeepButtonState(button, kept) {
    const icon = button.querySelector('i');
    const label = button.querySelector('.cj-keep-label');

    button.classList.toggle('cj-keep-active', kept);
    button.setAttribute('aria-pressed', kept ? 'true' : 'false');

    if (icon) {
        icon.classList.toggle('fas', kept);
        icon.classList.toggle('far', !kept);
    }

    if (label) {
        label.textContent = kept ? 'キープ中' : 'キープ';
    }
}

function cjToggleKeep(button) {
    const targetType = button.dataset.targetType || 'job';
    const targetId = parseInt(button.dataset.targetId || '0', 10);

    if (!targetId) {
        cjShowToast('対象を特定できませんでした。', 'danger');
        return;
    }

    const payload = { action: 'toggle' };

    if (targetType === 'shop') {
        payload.shop_id = targetId;
    } else {
        payload.job_id = targetId;
    }

    button.disabled = true;
    button.classList.add('cj-keep-loading');

    fetch('/api/keep/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const kept = !!data.kept;
                button.dataset.kept = kept ? '1' : '0';
                cjUpdateKeepButtonState(button, kept);
                cjShowToast(
                    data.message || (kept ? 'キープに追加しました。' : 'キープから削除しました。'),
                    kept ? 'success' : 'info'
                );

                if (!kept && button.dataset.removeOnUnkeep === '1') {
                    const selector = button.dataset.removeTarget || '.cj-keep-card';
                    const target = button.closest(selector);
                    if (target) {
                        target.classList.add('cj-keep-card--removing');
                        setTimeout(() => {
                            target.remove();
                            document.dispatchEvent(new CustomEvent('cj:keep:removed', {
                                detail: { targetType, targetId }
                            }));
                        }, 220);
                    } else {
                        document.dispatchEvent(new CustomEvent('cj:keep:toggled', {
                            detail: { targetType, targetId, kept }
                        }));
                    }
                } else {
                    document.dispatchEvent(new CustomEvent('cj:keep:toggled', {
                        detail: { targetType, targetId, kept }
                    }));
                }
            } else {
                if (data.requireLogin) {
                    cjRequireLoginModal();
                } else {
                    cjShowToast(data.message || 'エラーが発生しました。', 'danger');
                }
            }
        })
        .catch(error => {
            console.error('keep_toggle error:', error);
            let errorMessage = 'エラーが発生しました。';
            if (error.message) {
                errorMessage += ' (' + error.message + ')';
                console.error('Error details:', error);
            }
            cjShowToast(errorMessage, 'danger');
        })
        .finally(() => {
            button.disabled = false;
            button.classList.remove('cj-keep-loading');
        });
}

function cjInitKeepButtons() {
    const buttons = document.querySelectorAll('.cj-keep-toggle');
    if (!buttons.length) {
        return;
    }

    buttons.forEach(button => {
        const kept = button.dataset.kept === '1';
        cjUpdateKeepButtonState(button, kept);

        button.addEventListener('click', event => {
            event.preventDefault();

            if (!cjIsLoggedIn()) {
                cjRequireLoginModal();
                return;
            }

            cjToggleKeep(button);
        });
    });
}

function cjInitKeepSelection() {
    const selectAll = document.getElementById('cj-keep-select-all');
    const checkboxes = Array.from(document.querySelectorAll('.cj-keep-select'));
    const countDisplay = document.getElementById('cj-keep-selected-count');

    if (!selectAll && !checkboxes.length) {
        return;
    }

    const updateCount = () => {
        const checked = checkboxes.filter(cb => !cb.disabled && cb.checked).length;
        if (countDisplay) {
            countDisplay.textContent = checked.toString();
        }

        if (selectAll) {
            const selectable = checkboxes.filter(cb => !cb.disabled);
            selectAll.checked = selectable.length > 0 && selectable.every(cb => cb.checked);
            selectAll.indeterminate = selectable.some(cb => cb.checked) && !selectAll.checked;
        }
    };

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            checkboxes.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = selectAll.checked;
                }
            });
            updateCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    document.addEventListener('cj:keep:toggled', updateCount);
    document.addEventListener('cj:keep:removed', updateCount);

    updateCount();
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
document.addEventListener('DOMContentLoaded', () => {
    cjInitKeepButtons();
    cjInitKeepSelection();
});
