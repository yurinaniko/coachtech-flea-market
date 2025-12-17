@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">

@section('content')
<div class="purchase-cancel">
    <h2>購入がキャンセルされました</h2>
    <p>お支払いは完了していません。</p>

    <a href="{{ route('items.index') }}" class="btn">
        商品一覧に戻る
    </a>
</div>
@endsection