@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth">
    <h2 class="auth__title">メール認証が必要です</h2>
    <p>ご登録いただいたメールアドレスに認証メールを送信しました。</p>
    <p>メール内のリンクをクリックして認証を完了してください。</p>

    {{-- 再送信ボタン（後で機能付ける） --}}
    <form action="#" method="POST" class="auth__form">
        @csrf
        <button class="auth__button">認証メールを再送信する</button>
    </form>

    <a href="{{ route('logout') }}" class="auth__link">ログイン画面に戻る</a>
</div>
@endsection