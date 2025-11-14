@extends('layouts.app')

@section('title', 'チャット')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-comments me-2"></i>チャット
                </h1>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    応募した求人について店舗とやり取りできます
                </div>
            </div>

            @if($chatRooms->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">チャットルームがありません</h5>
                        <p class="text-muted">求人に応募すると、店舗とのチャットルームが作成されます。</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>求人を探す
                        </a>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>チャットルーム一覧
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($chatRooms as $room)
                            <div class="border-bottom p-3 chat-room-item" data-room-id="{{ $room['id'] }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 me-2">{{ $room['shop_name'] }}</h6>
                                            <span class="badge bg-{{ $room['application_status'] === 'accepted' ? 'success' : ($room['application_status'] === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ $room['application_status'] === 'accepted' ? '採用' : ($room['application_status'] === 'pending' ? '審査中' : '不採用') }}
                                            </span>
                                        </div>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-briefcase me-1"></i>
                                            {{ $room['job_title'] }}
                                        </p>
                                        @if($room['last_message'])
                                            <p class="mb-0 text-truncate" style="max-width: 400px;">
                                                {{ $room['last_message'] }}
                                            </p>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($room['last_message_time'])->diffForHumans() }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($room['unread_count'] > 0)
                                            <span class="badge bg-danger rounded-pill">{{ $room['unread_count'] }}</span>
                                        @endif
                                        <a href="{{ route('chat.show.id', ['id' => $room['id']]) }}" class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="fas fa-comment me-1"></i>チャットを開く
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .chat-room-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .chat-room-item:last-child {
        border-bottom: none !important;
    }
    </style>
@endsection

