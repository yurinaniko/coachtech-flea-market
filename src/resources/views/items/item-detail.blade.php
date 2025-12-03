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
            @if($item->purchase)
                <span class="sold-badge">sold</span>
            @endif
            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="item-detail__image">
        </div>

        {{-- 右カラム：商品情報 --}}
        <div class="item-detail__info">

            <h1 class="item-detail__name">{{ $item->name }}</h1>
            <p class="item-detail__brand">{{ $item->brand }}</p>

            <p class="item-detail__price">
                ¥{{ number_format($item->price) }}
                <span class="item-detail__tax">(税込)</span>
            </p>

            {{-- お気に入りボタン--}}
            <div class="item-detail__actions">
                <div class="favorite-wrapper">
                    {{-- ハートアイコン常に表示 --}}
                    <img src="{{ Auth::check() && Auth::user()->favorites->contains($item->id) ? asset('images/pink-heart.png') : asset('images/heart.png') }}" class="favorite-icon" alt="favorite">

                    {{-- カウントは常に表示 --}}
                    <span class="favorite-count">{{ $item->users->count() }}</span>

                    {{-- ログイン時のみボタン有効 --}}
                    @if (Auth::check())
                        @if (Auth::user()->favorites->contains($item->id))
                            <form action="{{ route('unfavorite', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="favorite-btn"></button>
                            </form>
                        @else
                            <form action="{{ route('favorite', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="favorite-btn"></button>
                            </form>
                        @endif
                    @endif
                </div>
                {{-- コメント --}}
                <div class="comment-wrapper">
                    <img src="{{ asset('images/comment.png') }}" alt="コメント" class="comment-icon">
                    <span class="comment-count">{{ $item->comments->count() }}</span>
                </div>
            </div>
            {{-- 購入ボタン or SOLD --}}
            @if ($item->purchase && $item->purchase->status === 'sold')
                <p class="sold-message">※この商品は売り切れました</p>
            @else
                <form action="{{ route('purchase.store', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="item-detail__buy-button">購入手続きへ</button>
                </form>
            @endif

            <label class="item-detail__section-title">商品説明</label>
            <p class="item-detail__description">{{ $item->description }}</p>
        <div class="item-detail__attributes">
            <label class="item-detail__section-title">商品の情報</label>
                {{-- ここは後でDB接続予定 --}}
                <p class="item-detail__category">
                    <strong>カテゴリ：</strong>
                    <span class="item-detail__category-tags">
                        @foreach ($item->categories as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </span>
                </p>
                <p><strong>商品の状態：</strong> {{ $item->condition }}</p>
            </div>

            {{-- コメント --}}
            <div class="item-detail__comment-wrapper">
                <label class="item-detail__section-title">コメント({{ $item->comments->count() }})</label>
                @if($comments->count() === 0)
                    <div class="item-detail__comment-item">
                {{-- コメントが0件のときのプレースホルダー --}}
                        <img src="{{ asset('images/user-icon.png') }}" class="comment-user-icon" alt="user">
                        <span class="comment-user-name">admin</span>
                    </div>
                    <div class="comment-placeholder-box">
                        <p class="comment-placeholder__text">こちらにコメントが入ります。</p>
                    </div>
                @else
                    <div class="item-detail__comment-item">
                        <img src="{{ asset('images/user-icon.png') }}" class="comment-user-icon" alt="user">
                        <span class="comment-user-name">admin</span>
                    </div>
                    <div class="comment-body">
                            <p class="comment-text">{{ $comments->first()->comment }}</p>
                    </div>

                    {{-- コメント一覧 --}}
                    <div id="comment-list">
                        @foreach ($comments->skip(1) as $index => $comment)
                            <div class="comment-item comment-hidden" @if ($index < 5) style="display:block" @endif>
                                <p class="comment-user">{{ $comment->user->name }}</p>
                                <p class="comment-text">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- もっと見るボタン --}}
                    @if ($comments->count() > 6)
                        <button id="show-more" class="comment-more-btn">もっと見る</button>
                    @endif
                @endif
            </div>
            {{-- コメント入力フォーム --}}
            <form action="{{ route('comment.store', $item->id) }}" method="POST" class="item-detail__comment-form">
                @csrf
                <label class="comment-label">商品へのコメント</label>
                <textarea name="comment" required></textarea>
                <button class="item-detail__comment-submit">コメントを追加する</button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const icon = document.querySelector('.favorite-icon');
    const count = document.querySelector('.favorite-count');

    if(icon) {
        icon.addEventListener('click', () => {
            icon.classList.add('active');
            count.classList.add('active');

            setTimeout(() => {
                icon.classList.remove('active');
                count.classList.remove('active');
            }, 400);
        });
    }
});
</script>
<script>
    const showMoreBtn = document.getElementById('show-more');
    const hiddenComments = document.querySelectorAll('.comment-hidden');

    let isOpen = false;

    showMoreBtn.addEventListener('click', () => {
        isOpen = !isOpen;

        hiddenComments.forEach((comment, index) => {
            if (index >= 5 && !isOpen) {
                comment.style.display = 'none';
            } else {
                comment.style.display = 'block';
            }
        });

        showMoreBtn.textContent = isOpen ? '閉じる' : 'もっと見る';
    });
</script>
@endsection