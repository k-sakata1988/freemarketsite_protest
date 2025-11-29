    @extends('layouts.app')

    @section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
    @endsection

    @section('content')
    <form id="payment-form" method="POST" action="{{ route('purchase.store', $item->id) }}">
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
                    <select name="payment_method_type" id="payment_method_type">
                        <option value="credit">クレジットカード</option>
                        <option value="convenience">コンビニ払い</option>
                    </select>

                    <div id="credit-card-details" class="mt-3">
                        <h4 class="mb-2">カード情報</h4>

                        <div id="card-element"></div>

                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>

                        <input type="hidden" name="payment_method_id" id="payment_method_id">
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
                            {{$address->address }}
                            {{ $address->building }} {{-- 建物名 (nullの場合も考慮) --}}
                        </p>
                        @else

                        <p>〒 XXX-YYYY</p>

                        <p>住所が未登録です。<a href="{{ route('address.update', $item->id) }}">変更する</a>から登録してください。</p>
                        @endif
                    </div>
                </div>
                </div>

            <hr>
            <div class="purchase-right">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>支払い方法</span>
                        <span id="selected-method">クレジットカード</span>
                    </div>
                </div>


                <button class="purchase-button" id="card-button" type="submit" data-secret="{{ $intent->client_secret }}">購入する</button>
            </div>
        </div>
    </form>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ config('cashier.key') }}");
    const elements = stripe.elements();

    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret; 
    const paymentMethodTypeSelect = document.getElementById('payment_method_type');
    const creditCardDetails = document.getElementById('credit-card-details');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const selectedPaymentMethod = paymentMethodTypeSelect.value;
        cardButton.disabled = true;

        if (selectedPaymentMethod === 'credit') {
            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: { 
                            name: "{{ $user->name }}",
                        }
                    }
                }
            );

            if (error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                cardButton.disabled = false;
            } else {
                document.getElementById('payment_method_id').value = setupIntent.payment_method;
                form.submit();
            }

        } else if (selectedPaymentMethod === 'convenience') {
            form.submit();
        } else {
            alert('支払い方法が選択されていません。');
            cardButton.disabled = false;
        }
    });

    paymentMethodTypeSelect.addEventListener('change', function() {
        const selectedMethod = this.value;

        if (selectedMethod === 'credit') {
            creditCardDetails.style.display = 'block';
        } else {
            creditCardDetails.style.display = 'none';
        }

        const selectedText = this.options[this.selectedIndex].text;
        document.getElementById('selected-method').textContent = selectedText;
    });

    paymentMethodTypeSelect.dispatchEvent(new Event('change'));
</script>
@endsection