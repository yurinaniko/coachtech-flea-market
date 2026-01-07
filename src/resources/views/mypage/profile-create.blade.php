@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile-form.css') }}">
@endsection

@section('content')
<div class="profile-form">
    <h2 class="profile-form__title">プロフィール設定</h2>
    @if (session('verified'))
        <div class="toast js-flash-message">
            メール認証が完了しました
        </div>
    @endif
    <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data" class="profile-form__body">
        @csrf
        {{-- プロフィール画像 --}}
        <div class="profile-form__image-area">
            @if(!empty($profile?->img_url))
                <img id="preview" src="{{ asset('storage/' . $profile->img_url) }}" class="profile-form__image" alt="プロフィール画像">
            @else
                <div id="preview" class="profile-form__placeholder"></div>
            @endif
            <label class="profile-form__image-label">
                画像を選択する
                <input type="file" name="image" accept="image/*" class="profile-form__image-input" onchange="previewImage(event)">
            </label>
            @error('image')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="profile-form__group">
            <label class="profile-form__label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="profile-form__input">
            @error('name')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="profile-form__group">
            <label class="profile-form__label">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', optional($profile)->postal_code ?? '') }}" class="profile-form__input">
            @error('postal_code')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="profile-form__group">
            <label class="profile-form__label">住所</label>
            <input type="text" name="address" value="{{ old('address', optional($profile)->address ?? '') }}" class="profile-form__input">
            @error('address')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="profile-form__group">
            <label class="profile-form__label">建物名</label>
            <input type="text" name="building" value="{{ old('building', optional($profile)->building ?? '') }}" class="profile-form__input">
            @error('building')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>
        <button class="profile-form__button">更新する</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('preview');
        if (preview.tagName === 'DIV') {
            preview.outerHTML =
                `<img id="preview" class="profile-edit__image" src="${e.target.result}">`;
        } else {
            preview.src = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}
</script>
{{-- トースト --}}
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
@endpush
