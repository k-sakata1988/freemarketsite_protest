@extends('layouts.app')

@section('css')
<style>
    .verification-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        text-align: center;
        padding: 20px;
    }

    .verification-container h2 {
        font-size: 20px;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .verification-container p {
        font-size: 20px;
        margin-bottom: 40px;
        color: #000;
        font-weight: bold;
    }

    .certification-button {
        padding: 15px 50px;
        font-size: 16px;
        font-weight: bold;
        background-color: #f0f0f0;
        color: #000;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 40px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .resend-form {
        margin: 0;
    }
    
    .resend-link {
        background: none;
        border: none;
        color: #3490dc;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        padding: 0;
    }
    
    .resend-link:hover {
        color: #2779bd;
        text-decoration: underline; /* ホバーで下線 */
    }
</style>
@endsection

@section('content')
<div class="verification-container">
    <h1>登録していただいたメールアドレスに認証メールを送信しました。</h1>
    <p>メール認証を完了してください。</p>

    <div class="certification-button">
        認証はこちらから
    </div>

    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
    </form>
</div>
@endsection