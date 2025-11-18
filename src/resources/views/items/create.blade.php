@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-main">
    <h1 class="page-title">商品の出品</h1>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="sell-form" novalidate>
        @csrf
        <section class="item-section">
            <h2 class="section-title">商品画像</h2>
            <div class="image-upload-area">
                <input type="file" name="image_path" id="image-upload" accept="image/*" style="display: none;">
                <label for="image-upload" class="image-upload-label">
                    <span type="button" class="select-image-btn" id="image-select-btn">画像を選択する</span>
                    <img id="image-preview" src="#" alt="商品画像プレビュー" style="display: none;">
                </label>
            </div>
            @error('image_path')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </section>

        <section class="item-section">
            <h2 class="section-title">商品の詳細</h2>
            <hr class="section-divider">

            <div class="form-group category-group">
                <label class="input-label">カテゴリー</label>
                <div class="category-tags">
                    @foreach ($categories as $category)
                    @php
                    $selected = old('category_id', $selectedCategories ?? []);
                    $isChecked = in_array($category->id, (array)$selected);
                    @endphp
                <label class="category-tag-label">
                    <input type="checkbox" name="category_id[]" value="{{ $category->id }}" style="display: none;" {{ $isChecked ? 'checked' : '' }}>
                    <span class="tag-content {{ $isChecked ? 'active' : '' }}">{{ $category->name }}</span>
                </label>
                @endforeach
                </div>
                @error('category_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="condition_id" class="input-label">商品の状態</label>
                <select name="condition_id" id="condition_id" class="form-select">
                    <option value="">選択してください</option>
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ (int)old('condition_id') === $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                    @endforeach
                </select>
                @error('condition_id')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="item-section">
            <h2 class="section-title">商品名と説明</h2>
            <hr class="section-divider">

            <div class="form-group">
                <label for="name" class="input-label">商品名</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="brand_name" class="input-label">ブランド名</label>
                <input type="text" name="brand_name" id="brand_name" class="form-input" value="{{ old('brand_name') }}" placeholder="任意">
            </div>

            <div class="form-group">
                <label for="description" class="input-label">商品の説明</label>
                <textarea name="description" id="description" class="form-textarea">{{ old('description') }}</textarea>
                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="item-section">
            <h2 class="section-title">販売価格</h2>
            <div class="form-group price-group">
                <div class="price-input-wrapper">
                    <span class="price-symbol">¥</span>
                    <input type="number" name="price" id="price" class="form-input price-input" value="{{ old('price') }}" min="100" step="1">
                </div>
                @error('price')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <button type="submit" class="submit-btn">出品する</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const imageUpload = document.getElementById('image-upload');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        const button = document.getElementById('image-select-btn');

        imageUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    button.textContent = '画像を再選択する';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                preview.src = '#';
                placeholder.style.display = 'block';
                button.textContent = '画像を選択する';
            }
        });

        document.querySelectorAll('.category-tag-label').forEach(label => {
            const input = label.querySelector('input[type="checkbox"]');
            const tagContent = label.querySelector('.tag-content');

            if (input.checked) {
                tagContent.classList.add('active');
            }

            label.addEventListener('click', (e) => {
                input.checked = !input.checked;
                tagContent.classList.toggle('active', input.checked);
                e.preventDefault();
            });
        });
    });
</script>
@endsection
