@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-settings-page">
    <form class="profile-form" method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h1>プロフィール設定</h1>

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-image-section">
            <div class="profile-image-container">
                @if ($user->profile_image)
                    <img src="{{ Storage::url($user->profile_image) }}" alt="現在の画像">
                @else
                    <div class="profile-placeholder">画像なし</div>
                @endif
            </div>

            <label for="profile_image" class="image-select-button">
                画像を選択する
            </label>
            <input type="file" name="profile_image" id="profile_image" style="display: none;">

            @error('profile_image')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_name">ユーザー名</label>
            <input type="text" name="user_name" value="{{ old('user_name', $user->name) }}" placeholder="既存の値が入力されている">
            @error('user_name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', optional($user->address)->postal_code) }}" placeholder="既存の値が入力されている">
            @error('postal_code')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', optional($user->address)->address) }}" placeholder="既存の値が入力されている">
            @error('address')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name', optional($user->address)->building) }}" placeholder="既存の値が入力されている">
            @error('building_name')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="update-button">
            更新する
        </button>
    </form>
</div>
@endsection