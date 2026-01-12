@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-list.css') }}">
@endsection

@section('content')
{{-- カテゴリータブ --}}
<div class="item-list__tabs-wrapper">
    <div class="item-list__tabs">
        @php
            $keyword = request('keyword');
        @endphp
            <a href="{{ route('mypage.index', ['tab' => 'recommend','keyword' => request('keyword')]) }}"
            class="item-list__tab {{ $page === 'recommend' ? 'item-list__tab--active' : '' }}">
                おすすめ
            </a>
            <a href="{{ route('mypage.index', ['tab' => 'mylist','keyword' => request('keyword')]) }}"
            class="item-list__tab {{ $page === 'mylist' ? 'item-list__tab--active' : '' }}">
                マイリスト
            </a>
    </div>
</div>
{{-- 商品カード一覧 --}}
<div class="item-list">
    <div class="item-list__grid">
        @foreach ($items as $item)
            <a href="{{ $item->purchase ? 'javascript:void(0);' : route('items.show', $item->id) }}"
            class="item-card__link {{ $item->purchase ? 'disabled' : '' }}">
                <div class="item-card__image">
                    @if($item->purchase)
                        <span class="sold-badge">sold</span>
                    @endif
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="">
                </div>
                <p class="item-card__name">{{ $item->name }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection