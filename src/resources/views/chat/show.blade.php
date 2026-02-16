@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto;">

    <h2 style="margin-bottom: 20px;">
        {{ $purchase->item->name }} の取引チャット
    </h2>

    <div style="border:1px solid #ccc; padding:20px; height:400px; overflow-y:auto; background:#f9f9f9;">

        @forelse($messages as $message)
            <div style="margin-bottom:15px; 
                        text-align: {{ $message->sender_id === auth()->id() ? 'right' : 'left' }};">
                
                <div style="
                    display:inline-block;
                    padding:10px 15px;
                    border-radius:15px;
                    max-width:70%;
                    background: {{ $message->sender_id === auth()->id() ? '#DCF8C6' : '#ffffff' }};
                    border:1px solid #ddd;
                ">
                    <div style="font-size:14px;">
                        {{ $message->message }}
                    </div>
                    <div style="font-size:10px; color:gray; margin-top:5px;">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>

            </div>
        @empty
            <p>まだメッセージはありません。</p>
        @endforelse

    </div>

    <!-- メッセージ送信フォーム -->
    <form action="{{ route('chat.store', $purchase) }}" method="POST" style="margin-top:20px;">
        @csrf
        <div style="display:flex; gap:10px;">
            <input 
                type="text" 
                name="message" 
                placeholder="メッセージを入力..." 
                style="flex:1; padding:10px; border:1px solid #ccc; border-radius:5px;"
                required
            >
            <button 
                type="submit" 
                style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px;"
            >
                送信
            </button>
        </div>
    </form>

</div>
@endsection
