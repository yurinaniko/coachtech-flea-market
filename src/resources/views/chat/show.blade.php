@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="chat__layout">
    <div class="chat__sidebar">
        <h2 class="chat__sidebar-title">その他の取引</h2>
        @foreach ($transactions as $transaction)
            <a href="{{ route('chat.show', $transaction->item_id) }}" class="chat__sidebar-item">
                <span class="chat__sidebar-name">{{ $transaction->item->name }}</span>
            </a>
        @endforeach
    </div>
    @php
        $hasUserImage = optional($user->profile)->img_url;
    @endphp
    <div class="chat__main">
        <div class="chat__info-wrapper">
            <div class="chat__info">
                <div class="chat__info-title">
                    @if ($hasUserImage)
                        <img src="{{ asset('storage/' . $hasUserImage) }}" class="chat__info-icon" alt="ユーザーアイコン">
                    @else
                        <div class="chat__info-placeholder"></div>
                    @endif
                    <h1 class="chat__info-username">
                        「{{ $user->name }}」さんとの取引画面
                    </h1>
                </div>
            </div>
            @php
                $isBuyer = $purchase->user_id === auth()->id();
                $isSeller = $purchase->item->user_id === auth()->id();
            @endphp
            <form action="{{ route('chat.review', $purchase->id) }}" method="POST">
                @csrf
                @if ($isBuyer)
                    @if (!$purchase->buyer_reviewed)
                        <button type="button" id="completeBtn" class="chat__complete-button">
                            取引を完了する
                        </button>
                    @else
                        <p class="chat__finish-text">評価済です</p>
                    @endif
                @endif
                @if ($isSeller)
                    @if ($purchase->seller_reviewed)
                        <p class="chat__finish-text">評価済です</p>
                    @endif
                @endif
            </form>
        </div>
        <div class="chat__item-wrapper">
            <img src="{{ asset('storage/' . $item->img_url) }}" class="chat__item-image" alt="商品画像">
            <div class="chat__item-info">
                <h2 class="chat__item-name">{{ $item->name }}</h2>
                <p class="chat__item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>
        <div class="chat__messages">
            @foreach ($comments as $comment)
                @php
                    $hasMessageUserImage = optional($comment->user->profile)->img_url;
                @endphp
                <div class="chat__message {{ $comment->user_id === Auth::id() ? 'chat__message--sent' :'chat__message--received' }}">
                    <div class="chat__message-inner">
                        <div class="chat__message-content">
                            @if ($hasMessageUserImage)
                                <img src="{{ asset('storage/' . $hasMessageUserImage) }}" class="chat__message-icon">
                            @else
                                <span class="chat__message-placeholder"></span>
                            @endif
                            <label class="chat__message-user">
                                {{ $comment->user->name }}
                            </label>
                        </div>
                        <div class="chat__message-body">
                            @if ($comment->comment)
                                <p class="chat__message-text">{{ $comment->comment }}</p>
                            @endif
                            @if ($comment->image)
                                <img src="{{ asset('storage/' . $comment->image) }}"
                                    class="chat__message-image">
                            @endif
                            @if($comment->user_id === Auth::id())
                                <div class="chat__message-actions">
                                    <button class="chat__edit" data-id="{{ $comment->id }}" data-comment="{{ $comment->comment }}">
                                        編集
                                    </button>
                                    <form action="{{ route('chat.destroy', $comment->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="chat__delete-button" onclick="return confirm('削除しますか？')">削除</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="chat__input-area">
            <div class="chat__form-group">
                @if ($errors->any())
                    <div class="chat__error">
                        @foreach ($errors->all() as $error)
                            <p class="chat__error-text">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
            <form action="{{ route('chat.store', $item->id) }}" class="chat__send-form" method="POST" enctype="multipart/form-data">
                @csrf
                <textarea name="comment" class="chat__send-input" placeholder="取引メッセージを入力してください">{{ old('comment') }}</textarea>
                <input type="hidden" name="comment_id" id="comment_id">
                <label class="chat__image-upload">
                    画像を追加
                    <input type="file" name="image" hidden>
                </label>
                <button type="submit" class="chat__send-button">
                    <img src="{{ asset('images/send-button.png') }}" alt="送信">
                </button>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="reviewModal">
    <div class="modal__content">
        <div class="modal__header">
            <p class="modal__title">取引が完了しました。</p>
        </div>
        <p class="modal__text">今回の取引相手はどうでしたか？</p>
        <form method="POST" action="{{ route('chat.review', $purchase->id) }}">
            @csrf
            <div class="modal__stars">
                @for ($i = 1; $i <= 5; $i++)
                    <label>
                        <input type="radio" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} hidden>
                        <img src="{{ asset('images/gray-star.png') }}" class="star" data-value="{{ $i }}">
                    </label>
                @endfor
            </div>
            <div class="modal__footer">
                <button type="submit" class="modal__button">送信する</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reviewModal');
    const btn = document.getElementById('completeBtn');
    const stars = document.querySelectorAll('.star');
    if (btn) {
        btn.addEventListener('click', function () {
            modal.classList.add('active');
        });
    }

    @if ($isSeller && !is_null($purchase->buyer_reviewed) && is_null($purchase->seller_reviewed))
        modal.classList.add('active');
    @endif

    stars.forEach((star, index) => {
        star.addEventListener('click', function () {
            const value = Number(this.dataset.value);
            stars.forEach((s, i) => {
                if (i < value) {
                    s.src = '/images/yellow-star.png';
                } else {
                    s.src = '/images/gray-star.png';
                }
            });
            this.previousElementSibling.checked = true;
        });
    });

    const checked = document.querySelector('input[name="rating"]:checked');
    if (checked) {
        const value = Number(checked.value);
        stars.forEach((s, i) => {
            if (i < value) {
                s.src = '/images/yellow-star.png';
            }
        });
    }

    document.querySelectorAll('.chat__message-image').forEach(img => {
        img.addEventListener('click', () => {
            window.open(img.src, '_blank');
        });
    });

    document.querySelectorAll('.chat__edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const comment = this.dataset.comment;
            document.querySelector('textarea[name="comment"]').value = comment;
            document.getElementById('comment_id').value = id;
        });
    });
});

const textarea = document.querySelector('.chat__send-input');
textarea.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';

    if (this.scrollHeight > 120) {
        this.style.overflowY = 'auto';
    } else {
        this.style.overflowY = 'hidden';
    }
});
</script>
@endpush