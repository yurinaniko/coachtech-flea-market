@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase">

    <h2 class="purchase__title">商品購入画面</h2>

    <div class="purchase__container">

        {{-- 左：商品情報 --}}
        <div class="purchase__left">

            <div class="purchase__item-box">
                <p class="purchase__label">商品名</p>
                <p class="purchase__value">{{ $item->name }}</p>
            </div>

            <div class="purchase__item-box">
                <p class="purchase__label">商品価格</p>
                <p class="purchase__value">¥{{ number_format($item->price) }}</p>
            </div>

            {{-- 支払い方法 --}}
            <div class="purchase__item-box">
                <p class="purchase__label">支払い方法</p>

                <form action="{{ route('purchase.store', $item->id) }}" method="POST" class="purchase__form">
                    @csrf

                {{-- 支払い方法 --}}
                <div class="purchase__item-box">
                    <label class="purchase__label">支払い方法</label>

                    <select name="payment_method" class="purchase_select" required>
                        <option value="">選択してください</option>
                        <option value="card">クレジットカード</option>
                        <option value="konbini">コンビニ払い</option>
                    </select>
                </div>

                {{-- 住所 --}}
                <div class="purchase__address">
                    <p class="purchase__address-label">配送先</p>
                    <p class="purchase__address-text">
                    〒{{ $address->postal_code }}<br>
                    {{ $address->address }}<br>
                    {{ $address->building }}<br>
                    {{ $user->name }} 様
                    </p>

                    <a href="{{ route('address.edit') }}" class="purchase__address-edit">変更する</a>
                </div>

                <button class="purchase__button">購入する</button>

                </form>

            </div>
        </div>


        {{-- 右：価格サマリー --}}
        <div class="purchase__right">
            <div class="purchase-summary">
                <p class="purchase-summary__label">商品価格</p>
                <p class="purchase-summary__price">¥{{ number_format($item->price) }}</p>

                <p class="purchase-summary__label">支払い方法</p>
                <p class="purchase-summary__method">コンビニ払い</p> {{-- JS or サーバー表示でもOK --}}
            </div>
        </div>

    </div>

</div>
@endsection
