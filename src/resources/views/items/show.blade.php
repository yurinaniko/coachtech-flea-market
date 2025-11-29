@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__container">

        {{-- 左カラム：商品画像 --}}
        <div class="item-detail__image-area">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-detail__image">
        </div>

        {{-- 右カラム：商品情報 --}}
        <div class="item-detail__info">

            <h1 class="item-detail__name">{{ $item->name }}</h1>
            <p class="item-detail__brand">{{ $item->brand }}</p>

            <p class="item-detail__price">
                ¥{{ number_format($item->price) }}
                <span class="item-detail__tax">(税込)</span>
            </p>

            {{-- お気に入りボタン（飾りでOK） --}}
            <div class="item-detail__actions">
                <div class="action-item">
                    <img src="{{ asset('images/heart.png') }}" alt="お気に入り" class="favorite-icon">
                    <span class="favorite-count">1</span>
                </div>
                <div class="action-item">
                    <img src="{{ asset('images/comment.png') }}" alt="コメント" class="comment-icon">
                    <span class="comment-count">1</span>
                </div>
            </div>

            <a href="#" class="item-detail__buy-button">購入手続きへ</a>

            <label class="item-detail__section-title">商品説明</label>
            <p class="item-detail__description">{{ $item->description }}</p>

            <label class="item-detail__section-title">商品の情報</label>
            <div class="item-detail__attributes">
                {{-- ここは後でDB接続予定 --}}
                <p><strong>カテゴリー：</strong> <span class="category-placeholder">未設定</span></p>
                <p><strong>商品の状態：</strong> {{ $item->condition }}</p>
            </div>

            {{-- コメント --}}
            <div class="item-detail__comment-wrapper">
                <label class="item-detail__section-title">コメント(1)</label>
                <div class="item-detail__comment-item">
                    <img src="{{ asset('images/user-icon.png') }}" class="comment-user-icon" alt="user">
                    <span class="comment-user-name">admin</span>
                </div>
                <div class="comment-body">
                    <p class="comment-text">こちらにコメントが入ります。</p>
                </div>
                <form class="item-detail__comment-form">
                    <label class="comment-label">商品へのコメント</label>
                    <textarea></textarea>
                    <button class="item-detail__comment-submit">コメントを追加する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection