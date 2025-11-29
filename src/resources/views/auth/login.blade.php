@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="auth auth--login">
        <h1 class="auth__title">ログイン</h1>
        <form action="#" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">メールアドレス</label>
                <input type="email" class="form-input" name="email">
            </div>
            <div class="form-group">
                <label class="form-label">パスワード</label>
                <input type="password" class="form-input" name="password">
            </div>
            <button class="auth__submit">ログインする</button>
        </form>
        <a href="{{ route('register') }}" class="auth__link">会員登録はこちら</a>
    </div>
</div>
@endsection