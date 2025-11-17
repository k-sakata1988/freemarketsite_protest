@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__image">
        <img src="{{ Str::startsWith($item->image_path, ['http://','https://']) ? $item->image_path : asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
    </div>

    <div class="item-detail__info">
        <h1 class="item-detail__name">{{ $item->name }}</h1>
        <p class="item-detail__brand">{{ $item->brand }}</p>
        <p class="item-detail__price">¥{{ number_format($item->price) }}（税込）</p>

    <div class="item-detail__actions">
    @auth
    <button class="like-button {{ auth()->user()->favorites->contains($item->id) ? 'liked' : '' }}" data-item-id="{{ $item->id }}" id="like-button">
        <i class="{{ auth()->user()->favorites->contains($item->id) ? 'fas' : 'far' }} fa-star"></i>
        <span class="like-count" id="like-count">{{ $item->favorites->count() }}</span>
    </button>
    @else
    <a href="{{ route('login') }}" class="like-button">
        <i class="far fa-star"></i>
        <span class="like-count">{{ $item->favorites->count() }}</span>
    </a>
    @endauth

    <div class="comment-count">
        <i class="far fa-comment"></i>
        <span id="comment-count">{{ $item->comments->count() }}</span>
    </div>
</div>

        @auth
            <a href="{{ route('purchase.create', $item->id) }}" class="btn-purchase">購入手続きへ</a>
        @else
            <a href="{{ route('login') }}" class="btn-purchase">ログインして購入</a>
        @endauth

        <div class="item-detail__desc">
            <h3>商品説明</h3>
            <p>{{ $item->description }}</p>
        </div>

        <div class="item-detail__info-section">
            <h3>商品の情報</h3>
            <p><strong>カテゴリー：</strong>
            @php
            $categoryNames = [];
            if (!empty($item->category_id)) {
                $categoryIds = explode(',', $item->category_id);
                $categoryNames = \App\Models\Category::whereIn('id', $categoryIds)->pluck('name')->toArray();
            }
            @endphp

            @if (!empty($categoryNames))
            @foreach ($categoryNames as $name)
            <span class="category-tag">{{ $name }}</span>
            @endforeach
            @else
            未設定
            @endif
            </p>
            <p><strong>商品の状態：</strong> {{ $item->condition ?? '未設定' }}</p>
        </div>

        <div class="comments">
            <h3 id="comments-toggle">
                コメント（<span id="display-comment-count">{{ $item->comments->count() }}</span>）
                <i class="fas fa-chevron-down" id="toggle-icon"></i>
            </h3>

            <div id="comments-content" class="accordion-content is-closed">
                @foreach($item->comments as $comment)
                    <div class="comment">
                        <div class="comment-user">
                            <div class="icon-user"></div>
                            <span class="username">{{ $comment->user->name }}</span>
                        </div>
                        <p class="comment-body">{{ $comment->comment }}</p>
                    </div>
                @endforeach
            </div>

            @auth
            <div class="comment-form">
                <h4>商品へのコメント</h4>
                <form id="comment-form">
                    @csrf
                    <textarea name="comment" placeholder="コメントを入力してください"></textarea>
                    <button type="submit" class="btn-comment">コメントを送信する</button>
                </form>
            </div>
            @else
                <p class="login-message">
                    コメントを投稿するには <a href="{{ route('login') }}">ログイン</a> してください。
                </p>
            @endauth
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentsToggle = document.getElementById('comments-toggle');
    const commentsContent = document.getElementById('comments-content');
    const toggleIcon = document.getElementById('toggle-icon');

    commentsToggle?.addEventListener('click', function() {
        const isClosed = commentsContent.classList.contains('is-closed');
        
        if (isClosed) {
            commentsContent.classList.remove('is-closed');
            commentsContent.classList.add('is-open');
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        } else {
            commentsContent.classList.remove('is-open');
            commentsContent.classList.add('is-closed');
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    });
});
</script>

@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const displayCommentCountEl = document.getElementById('display-comment-count'); 
    const commentCountIconEl = document.getElementById('comment-count'); 
    const likeButton = document.getElementById('like-button');
    const likeCountEl = document.getElementById('like-count');

    const commentsContent = document.getElementById('comments-content');
    const toggleIcon = document.getElementById('toggle-icon');
    
    commentForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("comments.store", ["item" => $item->id]) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            if (!res.ok) {
                const errorData = await res.json();
                
                const errorMsg = errorData.errors?.comment?.[0] ?? 'コメントの送信に失敗しました。';
                
                alert(errorMsg);
                
                return Promise.reject(errorData); 
            }
            return res.json();
        })
        .then(data => {

            if (!data) return;

            const commentBody = document.createElement('div');
            commentBody.classList.add('comment');
            commentBody.innerHTML = `
                <div class="comment-user">
                    <div class="icon-user"></div>
                    <span class="username">${data.user}</span>
                </div>
                <p class="comment-body">${data.body}</p>
            `;
            
            commentsContent.prepend(commentBody); 
            
            if (displayCommentCountEl) {
                const currentCount = parseInt(displayCommentCountEl.textContent) || 0;
                displayCommentCountEl.textContent = currentCount + 1;
            }
            if (commentCountIconEl) {
                const currentCountIcon = parseInt(commentCountIconEl.textContent) || 0;
                commentCountIconEl.textContent = currentCountIcon + 1;
            }

            if (commentsContent.classList.contains('is-closed')) {
                commentsContent.classList.remove('is-closed');
                commentsContent.classList.add('is-open');
                toggleIcon.classList.remove('fa-chevron-down');
                toggleIcon.classList.add('fa-chevron-up');
            }

            commentForm.reset();
        })
        .catch(err => {
            if (err.message && err.message !== '[object Object]') {
                console.error("ネットワークエラー:", err);
                console.log("バリデーションエラーを検知しました。", err);
            }
        });
    });

    if (likeButton) {
        likeButton.addEventListener('click', function() {
            const url = '{{ route("favorite.toggle", $item->id) }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ item_id: this.dataset.itemId })
            })
            .then(async res => {
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error("サーバーエラー詳細:", errorText);
                    throw new Error('いいね処理に失敗しました。');
                }
                return res.json();
            })
            .then(data => {
                const isLiked = data.liked;
                const newCount = data.count;

                const icon = this.querySelector('i.fa-star');
                if (icon) {
                    if (isLiked) {
                        this.classList.add('liked');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        this.classList.remove('liked');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                }

                if (likeCountEl) {
                    likeCountEl.textContent = newCount;
                }
            })
            .catch(err => {
                console.error(err);
                alert('いいね処理中にエラーが発生しました。詳細をコンソールで確認してください。');
            });
        });
    }
});
</script>
@endauth
@endsection