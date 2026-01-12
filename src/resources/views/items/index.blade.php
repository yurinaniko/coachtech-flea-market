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
        <a href="{{ route('items.index', ['tab' => 'recommend']) }}"
            class="item-list__tab {{ request('tab', 'recommend') === 'recommend' ? 'item-list__tab--active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', ['tab' => 'mylist']) }}"
            class="item-list__tab {{ request('tab') === 'mylist' ? 'item-list__tab--active' : '' }}">
            マイリスト
        </a>
    </div>
</div>
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