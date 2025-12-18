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
    {{-- 初回だけ表示 --}}
    @if (session('verified'))
        <p class="alert alert-success">
            メール認証が完了しました
        </p>
    @endif
    {{-- 状態表示（常設） --}}
    @if (auth()->user()->hasVerifiedEmail())
        <p class="verified">メール認証済み</p>
    @endif
    <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data" class="mypage-edit__form">
        @csrf
        {{-- プロフィール画像 --}}
        <div class="mypage-edit__image-area">

            {{-- プロフィール画像 or プレースホルダー --}}
            @if(!empty($profile?->img_url))
                <img id="preview" src="{{ asset('storage/' . $profile->img_url) }}" class="profile-edit__image" alt="プロフィール画像">
            @else
                <div id="preview" class="profile-placeholder"></div>
            @endif
            {{-- 画像選択ボタン --}}
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
            <input type="text" name="postal_code" value="{{ old('postal_code', optional($profile)->postal_code ?? '') }}" class="mypage-edit__input">
            @error('postal_code')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        {{-- 住所 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">住所</label>
            <input type="text" name="address" value="{{ old('address', optional($profile)->address ?? '') }}" class="mypage-edit__input">
            @error('address')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="mypage-edit__group">
            <label class="mypage-edit__label">建物名</label>
            <input type="text" name="building" value="{{ old('building', optional($profile)->building ?? '') }}" class="mypage-edit__input">
            @error('building')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="mypage-edit__button">更新する</button>
    </form>
</div>
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('preview');

        if (preview.tagName === 'DIV') {
            preview.outerHTML = `<img id="preview" class="profile-edit__image" src="${e.target.result}">`;
        } else {
            preview.src = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.js-flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 2000);
    }
});
</script>
@endsection