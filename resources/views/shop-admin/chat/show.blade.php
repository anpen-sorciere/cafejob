<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャット - {{ $room->user->last_name }} {{ $room->user->first_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .message-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
    }
    
    .message-item {
        margin-bottom: 1rem;
    }
    
    .message-image img {
        max-width: 200px;
        max-height: 200px;
        cursor: pointer;
    }
    
    .message-meta {
        font-size: 0.75em;
        opacity: 0.8;
    }
    
    .chat-container {
        height: 500px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }
    
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
    }
    
    .image-modal img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
    }
    
    .image-modal .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 30px;
        cursor: pointer;
    }
    </style>
</head>
<body>
    <!-- ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('shop-admin.dashboard') }}">
                <i class="fas fa-coffee me-2"></i>カフェコレ（CafeColle）
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('shop-admin.applications.index') }}">
                    <i class="fas fa-file-alt me-1"></i>応募管理
                </a>
                <a class="nav-link active" href="{{ route('shop-admin.chat.index') }}">
                    <i class="fas fa-comments me-1"></i>チャット
                </a>
                <a class="nav-link" href="{{ route('shop-admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i>ログアウト
                </a>
                <form id="logout-form" action="{{ route('shop-admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- エラーメッセージ -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- 成功メッセージ -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- ヘッダー -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-comments me-2"></i>チャット
                        </h1>
                        <p class="text-muted mb-0">
                            {{ $room->user->last_name }} {{ $room->user->first_name }} ({{ $room->user->username }}) - {{ $room->application->job->title }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('shop-admin.chat.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>チャット一覧に戻る
                        </a>
                        <a href="{{ route('shop-admin.applications.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-file-alt me-1"></i>応募管理に戻る
                        </a>
                    </div>
                </div>

                <!-- チャットエリア -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-white">
                                        <i class="fas fa-comment-dots me-2"></i>メッセージ
                                    </h5>
                                    <small class="text-white-50">
                                        {{ $room->user->last_name }} {{ $room->user->first_name }} ({{ $room->user->username }})
                                    </small>
                                </div>
                            </div>
                            <span class="badge bg-light text-primary">
                                {{ $room->application->status === 'accepted' ? '採用' : ($room->application->status === 'pending' ? '審査中' : '不採用') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <!-- メッセージ表示エリア -->
                        <div class="chat-container" id="chatContainer">
                            @if($messages->isEmpty())
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>まだメッセージがありません。</p>
                                    <p>メッセージを送信して会話を始めましょう。</p>
                                </div>
                            @else
                                @foreach($messages as $message)
                                    <div class="message-item mb-3 {{ $message['sender_type'] === 'shop_admin' ? 'text-end' : 'text-start' }}">
                                        <div class="d-flex align-items-start {{ $message['sender_type'] === 'shop_admin' ? 'justify-content-end' : 'justify-content-start' }}">
                                            @if($message['sender_type'] === 'user')
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px; font-size: 0.8em;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="message-bubble {{ $message['sender_type'] === 'shop_admin' ? 'bg-primary text-white' : 'bg-light border' }}">
                                               
                                               @if($message['message_type'] === 'image' && $message['file_path'])
                                                   <div class="message-image mb-2">
                                                       <img src="{{ Storage::url($message['file_path']) }}" 
                                                            alt="送信された画像" 
                                                            class="img-fluid rounded" 
                                                            onclick="openImageModal('{{ Storage::url($message['file_path']) }}')">
                                                   </div>
                                               @endif
                                               
                                               <div class="message-text">{{ nl2br(e($message['message'])) }}</div>
                                               
                                               <div class="message-meta mt-2">
                                                   <div class="d-flex justify-content-between align-items-center">
                                                       <span class="fw-bold">
                                                           {{ $message['sender_name'] }}
                                                       </span>
                                                       <span>{{ \Carbon\Carbon::parse($message['created_at'])->setTimezone('Asia/Tokyo')->format('m/d H:i') }}</span>
                                                   </div>
                                               </div>
                                           </div>
                                           
                                           @if($message['sender_type'] === 'shop_admin')
                                               <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center ms-2" 
                                                    style="width: 32px; height: 32px; font-size: 0.8em;">
                                                   <i class="fas fa-store"></i>
                                               </div>
                                           @endif
                                       </div>
                                   </div>
                               @endforeach
                           @endif
                       </div>
                   </div>
                   
                   <!-- メッセージ送信フォーム -->
                   <div class="card-footer bg-light">
                       <form method="POST" id="message-form" enctype="multipart/form-data">
                           @csrf
                           <input type="hidden" name="action" value="send_message">
                           
                           <!-- 画像アップロード -->
                           <div class="mb-3">
                               <div class="d-flex align-items-center">
                                   <label for="image-upload" class="btn btn-outline-secondary btn-sm me-2">
                                       <i class="fas fa-image me-1"></i>画像を選択
                                   </label>
                                   <input type="file" class="form-control form-control-sm d-none" name="image" id="image-upload" accept="image/*">
                                   <small class="text-muted">JPG, PNG, GIF（最大5MB）</small>
                               </div>
                               <div id="image-preview" class="mt-2" style="display: none;">
                                   <img id="preview-img" src="" alt="プレビュー" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                   <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearImagePreview()">
                                       <i class="fas fa-times"></i>
                                   </button>
                               </div>
                           </div>
                           
                           <!-- メッセージ入力 -->
                           <div class="input-group">
                               <textarea class="form-control" name="message" placeholder="メッセージを入力してください..." rows="2" style="resize: none;"></textarea>
                               <button class="btn btn-primary" type="submit" id="send-btn">
                                   <i class="fas fa-paper-plane"></i>
                               </button>
                           </div>
                           
                           <!-- 送信オプション -->
                           <div class="mt-2 d-flex justify-content-between align-items-center">
                               <small class="text-muted">
                                   <i class="fas fa-info-circle me-1"></i>
                                   メッセージまたは画像を送信できます
                               </small>
                               <div class="btn-group btn-group-sm">
                                   <button type="button" class="btn btn-outline-success" onclick="sendQuickMessage('面接のご連絡をいたします。')">
                                       <i class="fas fa-calendar me-1"></i>面接連絡
                                   </button>
                                   <button type="button" class="btn btn-outline-info" onclick="sendQuickMessage('ありがとうございます。')">
                                       <i class="fas fa-thumbs-up me-1"></i>ありがとう
                                   </button>
                               </div>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <!-- 画像拡大表示モーダル -->
   <div id="imageModal" class="image-modal">
       <span class="close" onclick="closeImageModal()">&times;</span>
       <img id="modalImage" src="" alt="拡大画像">
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
   <script>
   // 画像プレビュー機能
   document.getElementById('image-upload').addEventListener('change', function(e) {
       const file = e.target.files[0];
       if (file) {
           const reader = new FileReader();
           reader.onload = function(e) {
               document.getElementById('preview-img').src = e.target.result;
               document.getElementById('image-preview').style.display = 'block';
           };
           reader.readAsDataURL(file);
       }
   });

   function clearImagePreview() {
       document.getElementById('image-upload').value = '';
       document.getElementById('image-preview').style.display = 'none';
   }

   // 画像拡大表示
   function openImageModal(imageSrc) {
       document.getElementById('modalImage').src = imageSrc;
       document.getElementById('imageModal').style.display = 'block';
   }

   function closeImageModal() {
       document.getElementById('imageModal').style.display = 'none';
   }

   // モーダル外クリックで閉じる
   window.onclick = function(event) {
       const modal = document.getElementById('imageModal');
       if (event.target === modal) {
           modal.style.display = 'none';
       }
   }

   // クイックメッセージ送信
   function sendQuickMessage(message) {
       document.querySelector('textarea[name="message"]').value = message;
       document.getElementById('message-form').submit();
   }

   // チャットエリアを最下部にスクロール
   function scrollToBottom() {
       const chatContainer = document.getElementById('chatContainer');
       chatContainer.scrollTop = chatContainer.scrollHeight;
   }

   // ページ読み込み時に最下部にスクロール
   window.addEventListener('load', scrollToBottom);

   // フォーム送信時の処理
   document.getElementById('message-form').addEventListener('submit', function(e) {
       const message = document.querySelector('textarea[name="message"]').value.trim();
       const image = document.getElementById('image-upload').files[0];
       
       if (!message && !image) {
           e.preventDefault();
           alert('メッセージまたは画像を入力してください。');
           return;
       }
       
       const submitBtn = document.getElementById('send-btn');
       submitBtn.disabled = true;
       submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
   });
   </script>
</body>
</html>

