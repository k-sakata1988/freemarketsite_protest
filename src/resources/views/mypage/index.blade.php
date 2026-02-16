@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-main">

    <div class="profile-header">
        <div class="profile-info">
            <div class="profile-image-container">
                @if ($user->profile_image)
                    <img src="{{ Storage::url($user->profile_image) }}" alt="プロフィール画像">
                @else
                    <div class="profile-placeholder">画像なし</div>
                @endif
            </div>
            <h1 class="user-name">{{ $user->name ?? 'ユーザー名' }}</h1>
        </div>

        <a href="{{ route('mypage.profile.edit') }}" class="profile-edit-link">
            プロフィールを編集
        </a>
    </div>

    <div class="mypage-tabs">
        <ul id="tab-menu">
            <li class="mypage-tab-item tab-item active" data-tab="sold">出品した商品</li>
            <li class="mypage-tab-item tab-item" data-tab="bought">購入した商品</li>
            <li class="mypage-tab-item tab-item" data-tab="trading">
                取引中の商品
                @if(isset($unreadCount) && $unreadCount > 0)
                <span class="notification-badge">{{ $unreadCount }}</span>
                @endif
            </li>
        </ul>
    </div>

    <div id="tab-sold" class="tab-content active">
        <h3>出品した商品</h3>
        <div class="mypage-items-container">
            @forelse ($soldItems ?? [] as $item)
            <a href="{{ route('items.show', ['item' => $item->id]) }}" class="mypage-item-card-link">
                <div class="mypage-item-card">
                    <div class="mypage-item-image">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
            </a>
            @empty
            <p class="no-items">現在、出品中の商品はありません。</p>
            @endforelse
        </div>
    </div>

    <div id="tab-bought" class="tab-content" style="display: none;">
        <h3>購入した商品</h3>
        <div class="mypage-items-container">
            @forelse ($boughtItems ?? [] as $item)
            <a href="{{ route('items.show', ['item' => $item->id]) }}" class="mypage-item-card-link">
                <div class="mypage-item-card">
                    <div class="mypage-item-image">
                        @php
                        $imagePath = $item->image_path;
                        $isExternal = Str::startsWith($imagePath, ['http://', 'https://']);
                        @endphp
                        <img src="{{ $isExternal ? $imagePath : asset('storage/' . $imagePath) }}" alt="{{ $item->name }}">
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
            </a>
            @empty
            <p class="no-items">購入した商品履歴はありません。</p>
            @endforelse
        </div>
    </div>

    <div id="tab-trading" class="tab-content" style="display: none;">
    <h3>取引中の商品</h3>

    <div class="mypage-items-container">
        @forelse ($tradingItems ?? [] as $item)
            <a href="{{ route('chat.show', $item->id) }}" class="mypage-item-card-link">
                <div class="mypage-item-card">
                    <div class="mypage-item-image">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        @if($item->unread_count > 0)
                            <span class="item-badge">{{ $item->unread_count }}</span>
                        @endif
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
            </a>
        @empty
            <p class="no-items">取引中の商品はありません。</p>
        @endforelse
    </div>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.mypage-tab-item');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabType = tab.dataset.tab;

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            contents.forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });

            const activeContent = document.getElementById(`tab-${tabType}`);
            activeContent.style.display = 'block';
            activeContent.classList.add('active');
        });
    });

    contents.forEach(content => {
        if (!content.classList.contains('active')) {
            content.style.display = 'none';
        }
    });
});
</script>
@endsection