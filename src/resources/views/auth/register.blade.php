@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth__wrapper">
    <div class="auth auth--register">
        <h2 class="auth__title">会員登録</h2>
        <form action="/register" method="POST" class="auth__form" novalidate>
            @csrf
            <div class="form__group">
                <label class="form__label">ユーザー名</label>
                <input type="text" name="name" class="form__input" value="{{ old('name') }}">
                @error('name')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form__group">
                <label class="form__label">メールアドレス</label>
                <input type="email" name="email" class="form__input" value="{{ old('email') }}">
                @error('email')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form__group">
                <label class="form__label">パスワード</label>
                <input type="password" name="password" class="form__input">
                @error('password')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form__group">
                <label class="form__label">確認用パスワード</label>
                <input type="password" name="password_confirmation" class="form__input">
                @error('password_confirmation')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <button class="auth__submit">登録する</button>
            <a href="{{ route('login') }}" class="auth__link">ログインはこちら</a>
        </form>
    </div>
</div>
@endsection
