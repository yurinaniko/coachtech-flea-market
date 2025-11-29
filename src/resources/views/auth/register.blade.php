@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="auth auth--register">
        <h2 class="auth__title">会員登録</h2>

        <form action="#" method="POST" class="auth__form">
            @csrf

            <div class="form-group">
                <label class="form-label">ユーザー名</label>
                <input type="text" name="name" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">メールアドレス</label>
                <input type="email" name="email" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">パスワード</label>
                <input type="password" name="password" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">確認用パスワード</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>

            <button class="auth__submit">登録する</button>

            <a href="{{ route('login') }}" class="auth__link">ログインはこちら</a>
        </form>
    </div>
</div>
@endsection
