@extends('layouts.app')

@section('title', 'チャット')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- ヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-comments me-2"></i>チャット
                    </h1>
                    <p class="text-muted mb-0">
                        {{ $room->shop->name }} - {{ $room->application->job->title }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>チャット一覧に戻る
                    </a>
                </div>
            </div>

            <!-- チャットエリア -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-comment-dots me-2"></i>メッセージ
                        </h5>
                        <span class="badge bg-{{ $room->application->status === 'accepted' ? 'success' : ($room->application->status === 'pending' ? 'warning' : 'secondary') }}">
                            {{ $room->application->status === 'accepted' ? '採用' : ($room->application->status === 'pending' ? '審査中' : '不採用') }}
                        </span>
                    </div>
                </div>
                
                <!-- メッセージ表示エリア -->
                <div class="card-body" style="height: 500px; overflow-y: auto;" id="chat-messages">
                    @if($messages->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-comment-slash fa-2x mb-3"></i>
                            <p>まだメッセージがありません。<br>最初のメッセージを送信してみましょう。</p>
                        </div>
                    @else
                        @foreach($messages as $message)
                            <div class="message-item mb-3 {{ $message['sender_type'] === 'user' ? 'text-end' : 'text-start' }}">
                                <div class="d-inline-block">
                                    <div class="message-bubble {{ $message['sender_type'] === 'user' ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%; padding: 10px 15px; border-radius: 18px;">
                                        @if($message['message_type'] === 'image' && $message['file_path'])
                                            <div class="message-image mb-2">
                                                <img src="{{ Storage::url($message['file_path']) }}" 
                                                     alt="送信された画像" 
                                                     class="img-fluid rounded" 
                                                     style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                                     onclick="openImageModal('{{ Storage::url($message['file_path']) }}')">
                                            </div>
                                        @endif
                                        <div class="message-text">{{ nl2br(e($message['message'])) }}</div>
                                        <div class="message-meta mt-1" style="font-size: 0.8em; opacity: 0.7;">
                                            {{ $message['sender_name'] }}
                                            <span class="ms-2">{{ \Carbon\Carbon::parse($message['created_at'])->setTimezone('Asia/Tokyo')->format('m/d H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                
                <!-- メッセージ送信フォーム -->
                <div class="card-footer">
                    <form method="POST" id="message-form" enctype="multipart/form-data" action="{{ route('chat.send', ['id' => $room->id]) }}">
                        @csrf
                        <input type="hidden" name="action" value="send_message">
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" name="image" id="image-upload" accept="image/*">
                            <small class="text-muted">画像ファイル（JPG, PNG, GIF）最大5MB</small>
                        </div>
                        <div class="input-group">
                            <textarea class="form-control" name="message" placeholder="メッセージを入力してください..." rows="2"></textarea>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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

    <style>
    .message-bubble {
        word-wrap: break-word;
        word-break: break-word;
    }

    #chat-messages {
        scroll-behavior: smooth;
    }

    .message-item:last-child {
        margin-bottom: 0 !important;
    }

    .message-image img {
        transition: transform 0.2s;
    }

    .message-image img:hover {
        transform: scale(1.05);
    }
    </style>

    <script>
    // チャットエリアを最下部にスクロール
    function scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // 画像拡大表示
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    // ページ読み込み時に最下部にスクロール
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
    });

    // メッセージ送信時の処理
    document.getElementById('message-form').addEventListener('submit', function(e) {
        const messageTextarea = this.querySelector('textarea[name="message"]');
        const imageUpload = this.querySelector('input[name="image"]');
        const message = messageTextarea.value.trim();
        const hasImage = imageUpload.files.length > 0;
        
        if (message === '' && !hasImage) {
            e.preventDefault();
            alert('メッセージまたは画像を入力してください。');
            return;
        }
        
        // ファイルサイズチェック
        if (hasImage) {
            const file = imageUpload.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                e.preventDefault();
                alert('ファイルサイズが大きすぎます。5MB以下の画像を選択してください。');
                return;
            }
        }
        
        // 送信ボタンを無効化
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    });
    </script>
@endsection

