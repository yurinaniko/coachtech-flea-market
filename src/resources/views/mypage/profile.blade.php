@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-list.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile-form.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="user-info">
    <div class="user-info__wrapper">
        <div class="user-info__center">
            @php
                $hasImage = optional($profile)->img_url;
            @endphp
            @if ($hasImage)
                <img src="{{ asset('storage/' . $hasImage) }}" class="profile-view__icon">
            @else
                <div class="profile-form__placeholder"></div>
            @endif
            <div class="mypage__user-info">
                <label class="mypage__user-name">{{ $user->name }}</label>
                <div class="mypage__stars" data-rating="{{ $avgRating ?? 0 }}">
                    @for ($i = 1; $i <= 5; $i++)
                        <label>
                            <input type="radio" name="rating" value="{{ $i }}" hidden>
                            <img src="{{ asset('images/gray-star.png') }}" class="little-star">
                        </label>
                    @endfor
                </div>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
    </div>
</div>
<div class="mypage__tabs-wrapper">
    <ul class="mypage__tabs">
        <li class="mypage__tab {{ $page === 'sell' ? 'mypage__tab--active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'sell']) }}">出品した商品</a>
        </li>
        <li class="mypage__tab {{ $page === 'buy' ? 'mypage__tab--active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'buy']) }}">購入した商品</a>
        </li>
        <li class="mypage__tab {{ $page === 'trading' ? 'mypage__tab--active' : '' }}">
            <a href="{{ route('mypage.profile', ['page' => 'trading']) }}">
                取引中の商品
                @if ($unreadCount > 0)
                    <span class="mypage__badge">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</div>
<div class="item-list">
    <div class="item-list__grid">
        @foreach ($items as $item)
            @php
                $isCompleted = $item->purchase?->is_completed ?? false;
                $purchase = $item->purchase;
                $unread = $purchase
                    ? $purchase->comments
                    ->where('is_read', false)
                    ->where('user_id', '!=', Auth::id())
                    ->count()
                    : 0;
            @endphp
            <a href="{{ $page === 'trading'
            ? route('chat.show', $item->id)
            : route('items.show', $item->id) }}"
            class="item-list__link">
                <div class="item-list__image">
                    @if($item->purchase && in_array($item->purchase->status, ['pending', 'completed']))
                        <span class="sold-badge">sold</span>
                    @endif
                    @if ($page === 'trading' && $unread > 0)
                        <span class="item-list__badge">{{ $unread }}</span>
                    @endif
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="">
                    @if($isCompleted)
                        <div class="item-list__overlay">取引終了</div>
                    @endif
                </div>
                <p class="item-list__name">{{ $item->name }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
@push('scripts')
<script>
window.addEventListener('pageshow', function (event) {
    if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
        window.location.reload();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const starContainer = document.querySelector('.mypage__stars');
    if (!starContainer) return;

    const rating = Number(starContainer.dataset.rating) || 0;
    const stars = starContainer.querySelectorAll('.little-star');

    stars.forEach((star, index) => {
        if (index < rating) {
            star.src = "{{ asset('images/yellow-star.png') }}";
        } else {
            star.src = "{{ asset('images/gray-star.png') }}";
        }
    });
});
</script>
@endpush