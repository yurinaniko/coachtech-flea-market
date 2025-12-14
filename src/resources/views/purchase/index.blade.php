@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase">
    {{-- 支払い方法 + フォーム --}}
    <div class="purchase__container">
        {{-- 左：商品情報 --}}
        <div class="purchase__left">
            <div class="purchase__product">
                <div class="purchase__image-box">
                    @if (!empty($item->full_image_path))
                        <img src="{{ asset('storage/' . $item->full_image_path) }}" alt="{{ $item->name }}">
                    @else
                        <div class="purchase__image-placeholder">商品画像</div>
                    @endif
                </div>
                {{-- 右：商品名＋価格（プレースホルダー対応） --}}
                <div class="purchase__info-box">
                    <p class="purchase__name">
                    {{ $item->name ?: '商品名' }}
                    </p>
                    <p class="purchase__price">
                        @if (!is_null($item->price))
                            ¥{{ number_format($item->price) }}
                        @else
                            ¥0,000
                        @endif
                    </p>
                </div>
            </div>
            <div class="purchase__underline">
                <label class="purchase__label">支払い方法</label>
                    <form method="GET" action="{{ route('purchase.index', $item->id) }}" class="method-form">
                        <select name="payment_method" class="purchase__select" onchange="this.form.submit()">
                            <option value="">選択してください</option>
                            <option value="card" {{ $selectedMethod === 'card' ? 'selected' : '' }}>カード払い</option>
                            <option value="konbini" {{ $selectedMethod === 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
                        </select>
                    </form>
                        @error('payment_method')
                            <p class="purchase-error">{{ $message }}</p>
                        @enderror
            </div>
            <form action="{{ route('purchase.store', $item->id) }}" method="POST" class="purchase__form">
                @csrf
                <input type="hidden" name="postal_code" value="{{ $user->profile->postal_code }}">
                <input type="hidden" name="address" value="{{ $user->profile->address }}">
                <input type="hidden" name="building" value="{{ $user->profile->building }}">
                <input type="hidden" name="payment_method" value="{{ $selectedMethod }}">

                {{-- 表示用：住所 --}}
                <div class="purchase__item-box purchase__underline">
                    <div class="purchase__address-header">
                        <p class="purchase__address-label">配送先</p>
                        <a href="{{ route('mypage.address.edit') }}" class="purchase__address-edit">変更する</a>
                    </div>
                    <div class="purchase_address-box">
                        <p class="purchase_address-postal">〒{{ $user->profile->postal_code }}</p>
                        <p class="purchase_address-address">{{ $user->profile->address }}</p>
                        <p class="purchase_address-building">{{ $user->profile->building }}</p>
                    </div>
                    @error('address')
                        <p class="purchase-error">{{ $message }}</p>
                    @enderror
                </div>
        </div>
        {{-- 右：価格サマリー --}}
        <div class="purchase__right">
            <div class="purchase-summary">
                <div class="purchase-summary__row">
                    <p class="purchase-summary__label">商品価格</p>
                    <p class="purchase-summary__price">¥{{ number_format($item->price) }}</p>
                </div>
                <div class="purchase-summary__row">
                    <p class="purchase-summary__label">支払い方法</p>
                    <p class="purchase-summary__method">{{ $selectedMethod === 'card' ? 'カード払い' :($selectedMethod === 'konbini' ? 'コンビニ払い' : '選択してください') }}</p>
                </div>
            </div>
            <button type="submit" class="purchase__button">購入する</button>
        </div>
    </div>
    </form>
</div>
@endsection