@extends('layouts.app')

@section('css')
<style>
    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    h2 {
        font-size: 24px;
        margin-bottom: 30px;
        font-weight: bold;
    }
    .form-group {
        text-align: left;
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .form-group input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box; /* paddingを含めた幅にする */
        font-size: 16px;
    }
    .update-button {
        width: 100%;
        padding: 15px;
        background-color: #e85252; /* 画像のボタン色に似せて赤系に */
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 20px;
        font-weight: bold;
    }
    .update-button:hover {
        background-color: #d84242;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h2>住所の変更</h2>
    @if ($errors->any())
        <div class="alert alert-danger" style="color: red; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user.address.save') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="item_id" value="{{ $item->id }}">

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}" placeholder="例: 123-4567" required>
            @error('postal_code')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address ?? '') }}" placeholder="都道府県から番地まで" required>
            @error('address')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" id="building_name" value="{{ old('building_name', $user->building_name ?? '') }}" placeholder="任意">
            @error('building_name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="update-button">更新する</button>
    </form>
</div>
@endsection