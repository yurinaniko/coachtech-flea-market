@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-success">
    <div class="purchase-success__box">
        <h2 class="purchase-success__title">購入が完了しました</h2>

        <p class="purchase-success__text">
            ご購入ありがとうございます。<br>
            商品一覧より購入内容をご確認ください。
        </p>

        <a href="{{ route('mypage.index') }}" class="purchase-success__button">
            商品一覧へ戻る
        </a>
    </div>
</div>
@endsection