@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-wrapper">

    <div class="chat-sidebar">
        <h3 class="sidebar-title">その他の取引</h3>

        @foreach($tradingPurchases as $trading)
            <a href="{{ route('chat.show', $trading) }}" class="sidebar-item">
                {{ $trading->item->name }}
            </a>
        @endforeach
    </div>
    <div class="chat-main">

        <div class="chat-header">

            <div class="chat-partner-info">
                <div class="chat-partner-avatar">
                    @if($partner->profile_image)
                        <img src="{{ Storage::url($partner->profile_image) }}">
                    @else
                        <div class="avatar-placeholder"></div>
                    @endif
                </div>

                <h2 class="chat-title">
                    「{{ $partner->name }}」さんとの取引画面
                </h2>
            </div>

            @if(auth()->id() === $purchase->user_id)
                <form method="POST" action="{{ route('purchase.complete', $purchase) }}">
                    @csrf
                    <button type="submit" class="complete-btn">
                        取引を完了する
                    </button>
                </form>
            @endif
        </div>
        <div class="chat-item-info">

            <div class="chat-item-image">
                @php
                    $imagePath = $purchase->item->image_path ?? $purchase->item->image ?? null;
                    $isExternal = $imagePath && \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']);
                @endphp

                @if($imagePath)
                    <img src="{{ $isExternal ? $imagePath : asset('storage/' . $imagePath) }}">
                @endif
            </div>

            <div class="chat-item-detail">
                <h2>{{ $purchase->item->name }}</h2>
                <p class="chat-item-price">
                    ¥{{ number_format($purchase->item->price) }}
                </p>
            </div>

        </div>
        <div class="chat-messages">

            @forelse($messages as $message)

            <div class="message-row {{ $message->sender_id === auth()->id() ? 'my-message' : 'other-message' }}">

                @if($message->sender_id !== auth()->id())
                    <div class="message-avatar">
                        @if($message->sender->profile_image)
                            <img src="{{ Storage::url($message->sender->profile_image) }}">
                        @else
                            <div class="avatar-placeholder"></div>
                        @endif
                    </div>
                @endif

                <div class="message-content">
                    <div class="message-username">
                        {{ $message->sender->name }}
                    </div>
                    <div class="message-bubble">
                        {{ $message->message }}
                    </div>
                    <div class="message-footer">
                        <div class="message-time">
                            {{ $message->created_at->format('m/d H:i') }}
                        </div>

                        @if($message->sender_id === auth()->id())
                        <div class="message-actions">
                            <button type="button" class="edit-toggle-btn" onclick="toggleEdit({{ $message->id }})">
                                編集
                            </button>

                            <form action="{{ route('messages.destroy', $message) }}" method="POST" onsubmit="return confirm('削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">削除</button>
                            </form>
                        </div>

                        <form action="{{ route('messages.update', $message) }}" method="POST" class="edit-form" id="edit-form-{{ $message->id }}" style="display:none;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="message" value="{{ $message->message }}" class="edit-input" required>
                            <button type="submit" class="edit-btn">更新</button>
                        </form>
                        @endif
                    </div>

                </div>
                @if($message->sender_id === auth()->id())
                    <div class="message-avatar">
                        @if($message->sender->profile_image)
                            <img src="{{ Storage::url($message->sender->profile_image) }}">
                        @else
                            <div class="avatar-placeholder"></div>
                        @endif
                    </div>
                @endif

            </div>

            @empty
                <p class="no-message">まだメッセージはありません。</p>
            @endforelse

        </div>
        <form action="{{ route('chat.store', $purchase) }}" method="POST"  class="chat-form">
            @csrf
            <input type="text" name="message" placeholder="取引メッセージを記入してください" class="chat-input" required>
            <button type="submit" class="chat-send-btn">
                送信
            </button>
        </form>
    </div>
</div>
<script>
    function toggleEdit(messageId) {
        const form = document.getElementById('edit-form-' + messageId);

        if (form.style.display === 'none') {
            form.style.display = 'flex';
        } else {
            form.style.display = 'none';
        }
    }
</script>
@endsection
