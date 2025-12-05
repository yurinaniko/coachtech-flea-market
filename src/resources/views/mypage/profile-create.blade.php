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

    <form action="{{ route('mypage.profile.store') }}" method="POST" enctype="multipart/form-data" class="mypage-edit__form">
        @csrf

        {{-- プロフィール画像 --}}
        <div class="mypage-edit__image-area">

            <div class="mypage-edit__image-left">
                <img id="preview"
                src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/user-icon.png') }}"
                class="mypage_icon">
            </div>

            <label class="mypage-edit__image-label">
                画像を選択する
                <input type="file" name="image" accept="image/*" class="mypage-edit__image-input" onchange="previewImage(event)">
            </label>
        </div>

        {{-- 名前 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mypage-edit__input">
        </div>

        {{-- 郵便番号 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="mypage-edit__input">
        </div>

        {{-- 住所 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="mypage-edit__input">
        </div>

        {{-- 建物名 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}"class="mypage-edit__input">
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