    @extends('layouts.app')

    @section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
    @endsection

    @section('content')
    <form method="POST" action="{{ route('purchase.store', $item->id) }}">
    @csrf
    <div class="purchase-container">

        <div class="purchase-left">
            <div class="product-info">
                @php
                $isExternalUrl = preg_match('/^https?:\/\//', $item->image_path);
                $imageUrl = $isExternalUrl ? $item->image_path : asset('storage/' . $item->image_path);
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="product-image">
                <div class="product-details">
                    <h2>{{ $item->name }}</h2>
                    <p class="price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr>

            <div class="payment-section">
                <h3>支払い方法</h3>
                <select name="payment_method" id="payment_method">
                    <option value="">選択してください</option>
                    <option value="credit">クレジットカード</option>
                    <option value="convenience">コンビニ払い</option>
                </select>
            </div>

            <hr>

            <div class="address-section">
                <div class="address-header">
                    <h3>配送先</h3>
                    <a href="{{ route('address.update', $item->id) }}" class="change-link">変更する</a>
                </div>
                @if ($address)
                <p>〒 {{ $address->postal_code }}</p>
                <p>
                    {{ $address->address }}
                    {{ $address->building }} {{-- 建物名 (nullの場合も考慮) --}}
                </p>
                @else
                <p>〒 XXX-YYYY</p>
                <p>住所が未登録です。<a href="{{ route('address.update', $item->id) }}">変更する</a>から登録してください。</p>
                @endif
            </div>

            <hr>

        </div>

        <div class="purchase-right">
            <div class="summary-box">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>
                <div class="summary-row">
                    <span>支払い方法</span>
                    <span id="selected-method">未選択</span>
                </div>
            </div>

            <button class="purchase-button" type="submit">購入する</button>
        </div>
    </div>
    </form>

    <script>
    document.getElementById('payment_method').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex].text;
        document.getElementById('selected-method').textContent = selected;
    });
    </script>
    @endsection
