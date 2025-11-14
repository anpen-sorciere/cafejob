@extends('layouts.app')

@section('title', '画像監視')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-shield-alt me-2"></i>画像監視
                </h1>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    チャットで送信された画像を監視
                </div>
            </div>
        </div>
    </div>

    @if($images->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">送信された画像がありません</h5>
                        <p class="text-muted">チャットで画像が送信されると、ここに表示されます。</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($images as $image)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $image->sender_name ?? '送信者不明' }}</h6>
                                <small class="text-muted">
                                    {{ $image->created_at->setTimezone('Asia/Tokyo')->format('m/d H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($image->file_path && \Storage::disk('public')->exists($image->file_path))
                                <div class="text-center mb-3">
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         alt="送信された画像" 
                                         class="img-fluid rounded" 
                                         style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                         onclick="openImageModal('{{ Storage::url($image->file_path) }}')">
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p>画像ファイルが見つかりません</p>
                                </div>
                            @endif
                            
                            <div class="small text-muted">
                                <p class="mb-1">
                                    <strong>店舗:</strong> {{ $image->room->shop->name ?? '不明' }}
                                </p>
                                <p class="mb-1">
                                    <strong>送信者:</strong> 
                                    {{ $image->sender_type === 'user' ? 'ユーザー' : '店舗管理者' }}
                                </p>
                                @if($image->message)
                                    <p class="mb-0">
                                        <strong>メッセージ:</strong> {{ Str::limit($image->message, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                @if($image->file_path && \Storage::disk('public')->exists($image->file_path))
                                    <button class="btn btn-outline-primary btn-sm" 
                                            onclick="openImageModal('{{ Storage::url($image->file_path) }}')">
                                        <i class="fas fa-search-plus me-1"></i>拡大
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteImage({{ $image->id }})">
                                        <i class="fas fa-trash me-1"></i>削除
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- 画像拡大表示モーダル -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">画像を表示</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="拡大画像" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// 画像拡大表示
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// 画像削除
function deleteImage(messageId) {
    if (confirm('この画像を削除しますか？')) {
        fetch(`/admin/images/${messageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('削除に失敗しました: ' + (data.error || '不明なエラー'));
            }
        })
        .catch(error => {
            alert('エラーが発生しました: ' + error);
        });
    }
}
</script>
@endpush
@endsection

