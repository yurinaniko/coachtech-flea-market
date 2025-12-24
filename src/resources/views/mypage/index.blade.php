@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-list.css') }}">
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
{{-- カテゴリータブ --}}
<div class="item-list__tabs-wrapper">
    <div class="item-list__tabs">
        @php
            $keyword = request('keyword');
        @endphp
        <a href="{{ route('mypage.index', ['page' => 'recommend', 'keyword' => $keyword]) }}"
        class="tab {{ request('page') === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('mypage.index', ['page' => 'favorite', 'keyword' => $keyword]) }}"
        class="tab {{ request('page') === 'favorite' ? 'active' : '' }}">
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