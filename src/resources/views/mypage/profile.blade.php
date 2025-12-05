@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-list.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    {{-- ① プロフィール情報 --}}
<div class="user-info-wrapper">
    <div class="user-info">
        <div class="user-info__center">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/user-icon.png') }}"
            class="mypage__icon" alt="ユーザーアイコン">
            <p class="mypage__username">{{ $user->name }}</p>
        </div>
        <a href="{{ route('mypage.profile.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
    </div>
</div>
{{-- ② タブ --}}
<div class="mypage-tabs-wrapper">
    <ul class="mypage__tabs">
        <li class="mypage__tab {{ $page === 'sell' ? 'is-active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'sell']) }}">出品した商品</a>
        </li>
        <li class="mypage__tab {{ $page === 'buy' ? 'is-active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'buy']) }}">購入した商品</a>
        </li>
    </ul>
</div>
{{-- 商品カード一覧 --}}
<div class="item-list">
    <div class="item-list__grid">
        @foreach ($items as $item)
            <a href="{{ $item->purchase ? 'javascript:void(0);' : route('items.show', $item->id) }}"
                class="item-card__link {{ $item->purchase ? 'disabled' : '' }}">
                <div class="item-card">
                    <div class="item-card__image">
                        @if($item->purchase)
                                <span class="sold-badge">sold</span>
                            @endif
                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="img-content">
                    </div>
                    <p class="item-card__name">{{ $item->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>

@endsection