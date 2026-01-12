@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase">
    <div class="purchase__container">
        <div class="purchase__left">
            <div class="purchase__product">
                <div class="purchase__image-box">
                    @if($item->is_sold)
                        <span class="item__card-sold">sold</span>
                    @endif
                    <img src="{{ asset($item->full_image_path) }}" alt="{{ $item->name }}">
                </div>
                <div class="purchase__info-box">
                    <p class="purchase__name">
                        {{ $item->name ?: '商品名' }}
                    </p>
                    <p class="purchase__price">
                        ¥{{ number_format($item->price) }}
                    </p>
                </div>
            </div>
            <div class="purchase__underline">
                <h3 class="purchase__label">支払い方法</h3>
                <form method="GET" action="{{ route('purchase.index', $item->id) }}">
                    <select name="payment_method" class="purchase__select" id="js-payment-select" onchange="this.form.submit()">
                        <option value="">選択してください</option>
                        <option value="card" {{ $selectedMethod === 'card' ? 'selected' : '' }}>カード払い</option>
                        <option value="konbini" {{ $selectedMethod === 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
                    </select>
                </form>
                @error('payment_method')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="purchase__item-box purchase__underline">
                <div class="purchase__address-header">
                    <h3 class="purchase__address-label">配送先</h3>
                    <a href="{{ route('mypage.address.edit') }}" class="purchase__address-edit">変更する</a>
                </div>
                <div class="purchase__address-box">
                    <p class="purchase__address-postal">〒{{ $user->profile->postal_code }}</p>
                    <p class="purchase__address-address">{{ $user->profile->address }}</p>
                    <p class="purchase__address-building">{{ $user->profile->building }}</p>
                </div>
                @error('address')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <form action="{{ route('purchase.store', $item->id) }}" method="POST" class="purchase__form">
            @csrf
            <input type="hidden" name="postal_code" value="{{ $user->profile->postal_code }}">
            <input type="hidden" name="address" value="{{ $user->profile->address }}">
            <input type="hidden" name="building" value="{{ $user->profile->building }}">
            <input type="hidden" name="payment_method" id="js-payment-hidden" value="{{ $selectedMethod }}">
            <div class="purchase__right">
                <div class="purchase__summary">
                    <div class="purchase__summary-row">
                        <h3 class="purchase__summary-label">商品価格</h3>
                        <p class="purchase__summary-price">¥{{ number_format($item->price) }}</p>
                    </div>
                    <div class="purchase__summary-row">
                        <h3 class="purchase__summary-label">支払い方法</h3>
                        <p class="purchase__summary-method" id="js-payment-method">{{ $selectedMethod === 'card' ? 'カード払い' :($selectedMethod === 'konbini' ? 'コンビニ払い' : '選択してください') }}</p>
                    </div>
                </div>
                <button type="submit" class="purchase__button">購入する</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const select  = document.getElementById('js-payment-select');
    const hidden  = document.getElementById('js-payment-hidden');
    const display = document.getElementById('js-payment-method');
    const labels = {
        card: 'カード払い',
        konbini: 'コンビニ払い'
    };
    if (select.value) {
        display.textContent = labels[select.value];
        hidden.value = select.value;
    }
    select.addEventListener('change', () => {
        hidden.value = select.value;
        display.textContent = labels[select.value] ?? '選択してください';
    });
});
</script>
@endpush