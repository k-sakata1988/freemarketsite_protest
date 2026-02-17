@extends('layouts.app')

@section('content')
<div style="display:flex; min-height:100vh;">

    {{-- =====================
        サイドバー
    ====================== --}}
    <div style="width:250px; background:#8f8f8f; padding:20px; color:white;">
        <h3 style="margin-bottom:20px;">その他の取引</h3>

        @foreach($tradingPurchases as $trading)
            <a href="{{ route('chat.show', $trading) }}" style="text-decoration:none;">
                <div style="background:white; color:black; padding:10px; margin-bottom:10px; border-radius:5px;">
                    {{ $trading->item->name }}
                </div>
            </a>
        @endforeach
    </div>


    {{-- =====================
        メインエリア
    ====================== --}}
    <div style="flex:1; background:#f5f5f5; display:flex; flex-direction:column;">

        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; border-bottom:1px solid #ccc;">
            
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="width:50px; height:50px;border-radius:50%; overflow:hidden;">
                    @if($partner->profile_image)
                    <img src="{{ Storage::url($partner->profile_image) }}"style="width:100%; height:100%; object-fit:cover;">
                    @else
                    <div style="width:100%; height:100%; background:#ccc;"></div>
                    @endif
                </div>
                <h2>
                    「{{ $partner->name }}」さんとの取引画面
                </h2>
            </div>

            @if(auth()->id() === $purchase->user_id)
                <form method="POST" action="{{ route('purchase.complete', $purchase) }}">
                    @csrf
                    <button type="submit" style="background:#f26b6b; color:white; padding:10px 20px; border:none; border-radius:20px;">
                        取引を完了する
                    </button>
                </form>
            @endif
        </div>


        <div style="display:flex; gap:20px; padding:20px; border-bottom:1px solid #ccc;">
            
            <div style="width:150px; height:150px; background:#ddd;">
                <img src="{{ $purchase->item->image_url ?? '' }}" style="width:100%; height:100%; object-fit:cover;">
            </div>

            <div>
                <h2>{{ $purchase->item->name }}</h2>
                <p style="font-size:18px;">¥{{ number_format($purchase->item->price) }}</p>
            </div>

        </div>


        <div style="flex:1; padding:20px; overflow-y:auto;">

            @forelse($messages as $message)
                <div style="margin-bottom:25px;text-align: {{ $message->sender_id === auth()->id() ? 'right' : 'left' }};">
                    <div style="display:flex;justify-content: {{ $message->sender_id === auth()->id() ? 'flex-end' : 'flex-start' }};align-items:flex-start;gap:10px;">

                    @if($message->sender_id !== auth()->id())
                        <div style="width:40px; height:40px; border-radius:50%; overflow:hidden;">
                            @if($message->sender->profile_image)
                                <img src="{{ Storage::url($message->sender->profile_image) }}"style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="width:100%; height:100%; background:#ccc;"></div>
                            @endif
                        </div>
                    @endif

                    <div style="background:{{ $message->sender_id === auth()->id() ? '#d4f7d4' : 'white' }}; padding:12px 18px; border-radius:15px;max-width:60%;border:1px solid #ccc;">
                        <div style="font-weight:bold; font-size:13px;">
                            {{ $message->sender->name }}
                        </div>

                        <div>
                            {{ $message->message }}
                        </div>

                        <div style="font-size:12px; color:gray; margin-top:5px;">
                            {{ $message->created_at->format('m/d H:i') }}
                        </div>
                    </div>

                    @if($message->sender_id === auth()->id())
                        <div style="width:40px; height:40px; border-radius:50%; overflow:hidden;">
                            @if($message->sender->profile_image)
                                <img src="{{ Storage::url($message->sender->profile_image) }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="width:100%; height:100%; background:#ccc;"></div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @empty
                <p>まだメッセージはありません。</p>
            @endforelse
        </div>


        <form action="{{ route('chat.store', $purchase) }}" method="POST"
              style="display:flex; gap:10px; padding:15px; border-top:1px solid #ccc; background:white;">
            @csrf

            <input type="text" name="message"
                   placeholder="取引メッセージを記入してください"
                   style="flex:1; padding:10px; border:1px solid #ccc; border-radius:5px;"
                   required>

            <button type="submit"
                    style="padding:10px 20px; background:#333; color:white; border:none; border-radius:5px;">
                送信
            </button>
        </form>

    </div>
</div>
@endsection
