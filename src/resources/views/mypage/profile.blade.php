@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-list.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile-form.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="user-info__wrapper">
    <div class="user-info">
        <div class="user-info__center">
            @php
                $hasImage = optional($profile)->img_url;
            @endphp
            @if ($hasImage)
                <img src="{{ asset('storage/' . $hasImage) }}" class="profile-view__icon">
            @else
                <div class="profile-form__placeholder"></div>
            @endif
            <p class="mypage__username">{{ $user->name }}</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
    </div>
</div>
<div class="mypage__tabs-wrapper">
    <ul class="mypage__tabs">
        <li class="mypage__tab {{ $page === 'sell' ? 'is-active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'sell']) }}">出品した商品</a>
        </li>
        <li class="mypage__tab {{ $page === 'buy' ? 'is-active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'buy']) }}">購入した商品</a>
        </li>
    </ul>
</div>
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
                                <img src="{{ asset('storage/' . $item->img_url) }}" alt="">
                    </div>
                    <p class="item-card__name">{{ $item->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection