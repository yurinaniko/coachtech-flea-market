@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-wrapper auth-wrapper--verify">
    <div class="auth auth--verify">
        <p class="auth__text">ご登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p class="auth__text">メール認証を完了してください。</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth__button">
                認証はこちらから
            </button>
            <button type="submit" class="auth__link">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection