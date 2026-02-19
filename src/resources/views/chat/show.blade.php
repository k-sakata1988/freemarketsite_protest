@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-wrapper">

    <div class="chat-sidebar">
        <h1 class="sidebar-title">その他の取引</h1>
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

            @if($showCompleteButton)
            <form method="POST" action="{{ route('purchase.markComplete', $purchase) }}">
                @csrf
                <button type="submit" class="complete-btn">
                    取引を完了する
                </button>
            </form>
            @endif


            @if($purchase->status === 'completed')
                <p class="completed-label">この取引は完了しています</p>
            @endif
        </div>

        <!-- 商品情報 -->
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
                <p class="chat-item-price">¥{{ number_format($purchase->item->price) }}</p>
            </div>
        </div>

        <!-- メッセージ一覧 -->
        <div class="chat-messages">
            @forelse($messages as $message)
            <div class="message-row {{ $message->sender_id === auth()->id() ? 'my-message' : 'other-message' }}">
                <div class="message-block">
                    <div class="message-header">
                        <div class="message-avatar">
                            @if($message->sender->profile_image)
                                <img src="{{ Storage::url($message->sender->profile_image) }}">
                            @else
                                <div class="avatar-placeholder"></div>
                            @endif
                        </div>
                        <div class="message-username">{{ $message->sender->name }}</div>
                    </div>
                    <div class="message-bubble">
                        @if($message->message)
                            <div class="message-text">{{ $message->message }}</div>
                        @endif
                        @if($message->image_path)
                            <div class="message-image">
                                <img src="{{ Storage::url($message->image_path) }}" alt="chat image">
                            </div>
                        @endif
                    </div>
                    <div class="message-footer">
                        <div class="message-time">{{ $message->created_at->format('m/d H:i') }}</div>

                        @if($message->sender_id === auth()->id())
                        <div class="message-actions">
                            <button type="button" class="edit-toggle-btn" onclick="toggleEdit({{ $message->id }})">編集</button>
                            <form action="{{ route('messages.destroy', $message) }}" method="POST" onsubmit="return confirm('削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">削除</button>
                            </form>
                        </div>
                        <form action="{{ route('messages.update', $message) }}" method="POST" class="edit-form" id="edit-form-{{ $message->id }}" style="display:none;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="message" value="{{ $message->message }}" class="edit-input">
                            <button type="submit" class="edit-btn">更新</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <p class="no-message">まだメッセージはありません。</p>
            @endforelse
        </div>

        <!-- メッセージ入力 -->
        <form action="{{ route('chat.store', $purchase) }}" method="POST" enctype="multipart/form-data" class="chat-form">
            @csrf
            @error('message') <p class="error-message">{{ $message }}</p> @enderror
            @error('image') <p class="error-message">{{ $message }}（画像を再選択してください）</p> @enderror

            <input type="text" id="chat-message" name="message" value="{{ old('message') }}" placeholder="取引メッセージを記入してください" class="chat-input" @if($purchase->status === 'completed') disabled @endif>
            <input type="file" name="image" id="image-input" accept="image/*" style="display:none;" @if($purchase->status === 'completed') disabled @endif>
            <button type="button" class="image-add-btn" onclick="document.getElementById('image-input').click();" @if($purchase->status === 'completed') disabled @endif>画像を追加</button>
            <button type="submit" class="chat-send-btn" @if($purchase->status === 'completed') disabled @endif>
                <img src="{{ asset('images/send-icon.png') }}" alt="送信">
            </button>
        </form>
    </div>

    <!-- 取引完了モーダル -->
    @if($showRatingModal)
        <div id="ratingModal" class="rating-modal" style="display:flex;">
    @else
        <div id="ratingModal" class="rating-modal" style="display:none;">
    @endif
        <div class="rating-modal-content">
            <form id="ratingForm" method="POST" action="{{ route('purchase.complete', ['purchase' => $purchase->id]) }}">
                @csrf
                <input type="hidden" name="rating_for" value="{{ $ratingTarget }}">
                <div class="rating-header">取引が完了しました。</div>
                <div class="rating-body">
                    @if($ratingTarget === 'seller')
                        出品者の評価をお願いします
                    @else
                        購入者の評価をお願いします
                    @endif
                </div>
                <div class="star-rating">
                    <span class="star" data-value="1">★</span>
                    <span class="star" data-value="2">★</span>
                    <span class="star" data-value="3">★</span>
                    <span class="star" data-value="4">★</span>
                    <span class="star" data-value="5">★</span>
                </div>
                <input type="hidden" name="rating" id="ratingValue" value="1">
                <div class="rating-footer">
                    <button type="submit" class="rating-submit-btn">送信する</button>
                    <!-- <button type="button" class="rating-cancel-btn" onclick="closeRatingModal()">キャンセル</button> -->
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEdit(messageId) {
    const form = document.getElementById('edit-form-' + messageId);
    form.style.display = form.style.display === 'none' ? 'flex' : 'none';
}

function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star');
    let selectedRating = 0;
    stars.forEach(star => {
        star.addEventListener('mouseover', function () {
            const value = this.dataset.value;
            stars.forEach(s => s.classList.remove('hover'));
            stars.forEach(s => { if(s.dataset.value <= value) s.classList.add('hover'); });
        });
        star.addEventListener('mouseout', function () {
            stars.forEach(s => s.classList.remove('hover'));
        });
        star.addEventListener('click', function () {
            selectedRating = this.dataset.value;
            stars.forEach(s => s.classList.remove('active'));
            stars.forEach(s => { if(s.dataset.value <= selectedRating) s.classList.add('active'); });
            document.getElementById('ratingValue').value = selectedRating;
        });
    });

    const input = document.getElementById("chat-message");
    const storageKey = "chat_draft_{{ $purchase->id }}";
    const savedDraft = localStorage.getItem(storageKey);
    if (savedDraft && !input.value) input.value = savedDraft;
    input.addEventListener("input", () => localStorage.setItem(storageKey, input.value));
    input.form.addEventListener("submit", () => localStorage.removeItem(storageKey));

});
</script>
@endsection