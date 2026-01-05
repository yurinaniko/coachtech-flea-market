@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-sell.css') }}">
@endsection

@section('content')
<div class="sell">
    <h2 class="sell__title">商品の出品</h2>
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell__form">
        @csrf
        <div class="form-group">
            <label class="form-label">商品画像</label>
            <div class="image-upload">
                <div class="image-preview" id="imagePreview">
                    <label for="image" class="image-select-btn">画像を選択</label>
                </div>
                <input type="file" name="img_url" id="image" class="image-input" accept="image/*">
            </div>
            @error('img_url')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="sell__section">
            <h3 class="sell__section-title">商品の詳細</h3>
            <div class="sell__section-divider"></div>
            <div class="form-group">
                <label class="form-label">カテゴリー</label>
                <div class="category-tags">
                    @php
                        $oldCategories = old('categories', []);
                    @endphp
                    @foreach ($categories as $category)
                        <label class="category-tag">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $oldCategories) ? 'checked' : '' }} >
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('categories')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">商品の状態</label>
                <select name="condition_id" class="form-input">
                    <option value="">選択してください</option>
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                        {{ $condition->condition }}
                        </option>
                    @endforeach
                </select>
                @error('condition_id')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="sell__section">
            <h3 class="sell__section-title">商品名と説明</h3>
            <div class="sell__section-divider"></div>
                <div class="form-group">
                    <label class="form-label">商品名</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}">
                    @error('name')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">ブランド名</label>
                    <input type="text" name="brand" class="form-input" value="{{ old('brand') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">商品説明</label>
                    <textarea name="description" class="form-input">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">販売価格</label>
                    <input type="text" name="price" class="form-input" placeholder="¥" value="{{ old('price') }}">
                    @error('price')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
        </div>
        <button type="submit" class="sell__button">出品する</button>
    </form>
</div>
<script>
const imageInput = document.getElementById('image');
const previewBox = document.getElementById('imagePreview');
const uploadBox = document.querySelector('.image-upload');
imageInput.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        previewBox.style.backgroundImage = `url(${event.target.result})`;
        previewBox.classList.add('has-image');
        uploadBox.style.border = 'none';
        uploadBox.style.background = 'none';
        const btn = previewBox.querySelector('.image-select-btn');
        if (btn) btn.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
window.addEventListener('load', () => {
    if (!imageInput.value) {
        previewBox.style.backgroundImage = 'none';
        previewBox.classList.remove('has-image');

        uploadBox.style.border = '';
        uploadBox.style.background = '';

        const btn = previewBox.querySelector('.image-select-btn');
        if (btn) btn.style.display = '';
    }
});
</script>
@endsection