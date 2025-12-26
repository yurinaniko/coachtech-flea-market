@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection
@php
    $status = $status ?? 'success';
@endphp
@section('content')
<div class="purchase-success">
    <div class="purchase-success__box">
        <h2 class="purchase-success__title">
            @if($status === 'success')
                購入が完了しました
            @else
                購入はキャンセルされました
            @endif
        </h2>
        <p class="purchase-success__text">
            @if($status === 'success')
                ご購入ありがとうございます。<br>
                商品一覧より購入内容をご確認ください。
            @else
                決済は完了していません。<br>
                必要であれば再度ご購入ください。
            @endif
        </p>

        <a href="{{ route('mypage.index') }}" class="purchase-success__button">
            商品一覧へ戻る
        </a>
    </div>
</div>
@endsection