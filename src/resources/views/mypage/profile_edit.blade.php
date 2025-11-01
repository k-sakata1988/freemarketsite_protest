@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <h2>プロフィール設定</h2>
    @if (session('success'))
        <div style="color: green; border: 1px solid green; padding: 10px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="text-align: center; margin-bottom: 20px;">
        @if ($user->profile_image)
            <img src="{{ Storage::url($user->profile_image) }}" alt="現在の画像" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
        @else
            
        @endif
        <br>
        <label for="profile_image" style="display: inline-block; padding: 5px 10px; border: 1px solid #d9534f; color: #d9534f; cursor: pointer;">
            画像をを選択する
        </label>
        <input type="file" name="profile_image" id="profile_image" style="display: none;">

        @error('profile_image')
            <div style="color: red; font-size: 0.9em;">{{ $message }}</div>
        @enderror
    </div>

    {{-- ユーザー名 --}}
    <div>
        <label for="user_name">ユーザー名</label>
        <input type="text" name="user_name" value="{{ old('user_name', $user->user_name) }}" placeholder="既存の値が入力されている">
        @error('user_name')<div style="color: red;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label for="postal_code">郵便番号</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="既存の値が入力されている">
        @error('postal_code')<div style="color: red;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label for="address">住所</label>
        <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="既存の値が入力されている">
        @error('address')<div style="color: red;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label for="building_name">建物名</label>
        <input type="text" name="building_name" value="{{ old('building_name', $user->building_name) }}" placeholder="既存の値が入力されている">
        @error('building_name')<div style="color: red;">{{ $message }}</div>@enderror
    </div>

    <button type="submit" style="background-color: #d9534f; color: white; padding: 10px 20px; border: none; cursor: pointer; margin-top: 20px;">
        更新する
    </button>
</form>
@endsection
