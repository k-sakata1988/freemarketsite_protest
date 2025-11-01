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
            <h2 class="user-name">{{ $user->user_name ?? 'ユーザー名' }}</h2>
        </div>

        <a href="{{ route('mypage.profile.edit') }}" class="profile-edit-link">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ切り替え --}}
    <div class="mypage-tabs">
        <ul id="tab-menu">
            <li class="mypage-tab-item tab-item active" data-tab="sold">出品した商品</li>
            <li class="mypage-tab-item tab-item" data-tab="bought">購入した商品</li>
        </ul>
    </div>

    {{-- 出品タブ --}}
    <div id="tab-sold" class="tab-content active">
        <h3>出品した商品</h3>
        <div class="mypage-items-container">
            @forelse ($soldItems ?? [] as $item)
                <div class="mypage-item-card">
                    <div class="mypage-item-image">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
            @empty
                <p class="no-items">現在、出品中の商品はありません。</p>
            @endforelse
        </div>
    </div>

    {{-- 購入タブ --}}
    <div id="tab-bought" class="tab-content" style="display: none;">
        <h3>購入した商品</h3>
        <div class="mypage-items-container">
            @forelse ($boughtItems ?? [] as $item)
                <div class="mypage-item-card">
                    <div class="mypage-item-image">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
            @empty
                <p class="no-items">購入した商品履歴はありません。</p>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab-item');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabType = tab.dataset.tab;

            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            tab.classList.add('active');
            document.getElementById(`tab-${tabType}`).classList.add('active');
        });
    });
});
</script>
@endsection