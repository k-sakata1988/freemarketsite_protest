@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="tabs">
    <button class="tab-button {{ $activeTab === 'recommended' ? 'active' : '' }}" data-tab="recommended">
        おすすめ
    </button>

    <button class="tab-button {{ $activeTab === 'mylist' ? 'active' : '' }}" 
        data-tab="mylist"
        @guest
            data-requires-login="true"
        @endguest>
        マイリスト
    </button>
</div>

<div id="items-container">
    @include('items._items', ['items' => $items])
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.tab-button');
    const container = document.getElementById('items-container');
    const keywordInput = document.querySelector('.header__search-input');
    const tabInput = document.querySelector('input[name="tab"]');

    const fetchItems = (tabType, keyword) => {
        let url = `/items/tab/${tabType}`;
        if (keyword) url += `?keyword=${encodeURIComponent(keyword)}`;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                if (tabInput) tabInput.value = tabType;
            })
            .catch(err => {
                console.error('タブ取得エラー:', err);
            });
    };

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const requiresLogin = button.dataset.requiresLogin === "true";
            const tabType = button.dataset.tab;
            const keyword = keywordInput ? keywordInput.value : '';

            if (requiresLogin) {
                window.location.href = '/login';
                return;
            }

            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            fetchItems(tabType, keyword);
        });
    });

    if (keywordInput) {
        keywordInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const activeButton = document.querySelector('.tab-button.active');
                const tabType = activeButton ? activeButton.dataset.tab : 'recommended';
                const keyword = keywordInput.value;

                fetchItems(tabType, keyword);
            }
        });
    }
});
</script>
@endsection