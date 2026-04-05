@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__container">
        <div class="item-detail__image-area">
            @if($item->purchase && in_array($item->purchase->status, ['pending', 'completed']))
                <span class="sold-badge">sold</span>
            @endif
            <img src="{{ asset('storage/'.$item->img_url) }}" alt="{{ $item->name }}" class="item-detail__image">
        </div>
        <div class="item-detail__info">
            <h1 class="item-detail__name">{{ $item->name }}</h1>
            <p class="item-detail__brand">{{ $item->brand }}</p>
            <p class="item-detail__price">
                ¥{{ number_format($item->price) }}
                <span class="item-detail__tax">(税込)</span>
            </p>
            <div class="item-detail__actions">
                <div class="item-detail__favorite">
                    @php
                        $isOwnItem = Auth::check() && Auth::id() === $item->user_id;
                        $isPurchasedByMe = Auth::check() && $item->purchase && $item->purchase->user_id === Auth::id();
                        $isFavorite = Auth::check() && Auth::user()->favorites->contains('id', $item->id);
                    @endphp
                    @auth
                        <form action="{{ route('favorite.toggle', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="item-detail__favorite-btn" @if($isOwnItem || $isPurchasedByMe) disabled @endif>
                                <img src="{{ $isFavorite ? asset('images/pink-heart.png') : asset('images/heart.png') }}" class="item-detail__favorite-icon" alt="favorite">
                                <span class="item-detail__favorite-count">
                                {{ $item->favorites_count ?? 0 }}
                                </span>
                            </button>
                        </form>
                    @endauth
                    @guest
                        <button class="item-detail__favorite-btn item-detail__favorite-btn--guest" disabled>
                            <img src="{{ asset('images/heart.png') }}" class="item-detail__favorite-icon" alt="favorite">
                            <span class="item-detail__favorite-count">
                                {{ $item->favorites_count ?? 0 }}
                            </span>
                        </button>
                    @endguest
                </div>
                <div class="item-detail__comment-action">
                    <img src="{{ asset('images/comment.png') }}" alt="コメント" class="item-detail__comment-icon">
                    <span class="item-detail__comment-count">{{ $item->comments_count ?? 0 }}</span>
                </div>
            </div>
            @if ($item->user_id === Auth::id())
                <p class="item-detail__notice item-detail__notice--own">
                    ※これはあなたが出品した商品です
                </p>
            @elseif ($item->purchase && $item->purchase->user_id === Auth::id())
                <p>※あなたが購入した商品です</p>
            @elseif ($item->purchase)
                <p>※この商品は購入済みです</p>
            @else
                <form action="{{ route('purchase.index', $item->id) }}" method="GET">
                    @csrf
                    <button type="submit" class="item-detail__buy-button">
                        購入手続きへ
                    </button>
                </form>
            @endif
            <h2 class="item-detail__section-title">商品説明</h2>
            <p class="item-detail__description">{{ $item->description }}</p>
            <div class="item-detail__attributes">
                <h2 class="item-detail__section-title">商品の情報</h2>
                <p class="item-detail__category">
                    <strong>カテゴリ</strong>
                    <span class="item-detail__category-tags">
                        @foreach ($item->categories ?? [] as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </span>
                </p>
            </div>
            <div class="item-detail__condition">
                <strong class="item-detail__label">商品の状態</strong>
                <span class="item-detail__value">{{ $item->condition->condition }}</span>
            </div>
            @php
                $comments = $comments ?? collect();
            @endphp
            <div class="item-detail__comment-wrapper">
                <h2 class="item-detail__section-title">コメント({{ $comments->count() }})</h2>
                    @if ($comments->count() === 0)
                        <div class="item-detail__comment-item">
                            <div class="comment-user-icon profile-placeholder"></div>
                                <span class="comment-user-name">admin</span>
                        </div>
                        <div class="comment-placeholder-box">
                            <p class="comment-placeholder__text">こちらにコメントが入ります。</p>
                        </div>
                    @else
                        @foreach ($comments->take(5) as $comment)
                            @php
                                $profile = $comment->user->profile;
                            @endphp
                            <div class="item-detail__comment-item">
                                @if ($profile && $profile->img_url)
                                    <img src="{{ asset('storage/' . $profile->img_url) }}" class="comment-user-icon">
                                @else
                                    <div class="comment-user-icon profile-placeholder"></div>
                                @endif
                                <span class="comment-user-name">{{ $comment->user->name }}</span>
                            </div>
                            <div class="comment-placeholder-box">
                                <p class="comment-placeholder__text">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                        @foreach ($comments->slice(5) as $comment)
                            @php
                                $profile = $comment->user->profile;
                            @endphp
                            <div class="comment-hidden" style="display:none;">
                                <div class="item-detail__comment-item">
                                    @if ($profile && $profile->img_url)
                                        <img src="{{ asset('storage/' . $profile->img_url) }}" class="comment-user-icon">
                                    @else
                                        <div class="comment-user-icon profile-placeholder"></div>
                                    @endif
                                    <span class="comment-user-name">{{ $comment->user->name }}</span>
                                </div>
                                <div class="comment-placeholder-box">
                                    <p class="comment-placeholder__text">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @endforeach
                        @if ($comments->count() > 5)
                            <button id="show-more" class="comment-more-btn">もっと見る</button>
                        @endif
                        <button id="close-comments" class="comment-close-btn" style="display:none;">閉じる</button>
                    @endif
                    <label class="comment-label">商品へのコメント</label>
                    <form action="{{ route('comment.store', $item->id) }}" method="POST" class="item-detail__comment-form">
                        @csrf
                        <textarea class="comment-textarea" name="comment"></textarea>
                        @error('comment')
                            <p class="form__error">{{ $message }}</p>
                        @enderror
                        @auth
                            <button type="submit" class="item-detail__comment-submit">
                                コメントを追加する
                            </button>
                        @endauth
                    </form>
                    @guest
                        <button class="item-detail__comment-submit disabled" disabled>
                            コメントを追加する
                        </button>
                    @endguest
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const icon = document.querySelector('.item-detail__favorite-icon');
    const count = document.querySelector('.item-detail__favorite-count');
    if (icon && count) {
        icon.addEventListener('click', () => {
                count.classList.add('item-detail__favorite-count--active');
            setTimeout(() => {
                count.classList.remove('item-detail__favorite-count--active');
            }, 400);
        });
    }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const showMoreBtn = document.getElementById('show-more');
    const closeBtn = document.getElementById('close-comments');
    const hiddenComments = document.querySelectorAll('.comment-hidden');
    if (showMoreBtn) {
        showMoreBtn.addEventListener('click', function () {
            hiddenComments.forEach(el => el.style.display = 'block');
            showMoreBtn.style.display = 'none';
            closeBtn.style.display = 'block';
        });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            hiddenComments.forEach(el => el.style.display = 'none');
            closeBtn.style.display = 'none';
            if (showMoreBtn) showMoreBtn.style.display = 'block';
        });
    }
});
</script>
@endpush