@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="mypage-edit">
    <h2 class="mypage-edit__title">プロフィール設定</h2>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mypage-edit__form">
        @csrf
        @method('PUT')
        {{-- プロフィール画像 --}}
        <div class="mypage-edit__image-area">
            <div class="mypage-edit__image-left">
                <img id="preview" src="{{ $profile && $profile->img_url ? asset('storage/' . $profile->img_url) : asset('images/user-icon.png') }}" alt="プロフィール画像">
            </div>
            <label class="mypage-edit__image-label">
                画像を選択する
                <input type="file" name="image" accept="image/*" class="mypage-edit__image-input" onchange="previewImage(event)">
            </label>
                @error('image')
                <p class="form__error">{{ $message }}</p>
                @enderror
        </div>
        {{-- 名前 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mypage-edit__input">
            @error('name')
            <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        {{-- 郵便番号 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}"  class="mypage-edit__input">
            @error('postal_code')
            <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">住所</label>
            <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}" class="mypage-edit__input">
            @error('address')
            <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">建物名</label>
            <input type="text" name="building" value="{{ old('building', $profile->building ?? '') }}" class="mypage-edit__input">
            @error('building')
            <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="mypage-edit__button">更新する</button>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview').setAttribute('src', e.target.result);
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection