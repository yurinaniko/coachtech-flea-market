@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">

    {{-- タブ --}}
    <div class="mypage__tabs">
        <a href="{{ route('mypage.index', ['page' => 'recommend']) }}"
            class="mypage__tab {{ request('page') === 'recommend' ? 'is-active' : '' }}">
            おすすめ
        </a>

        <a href="{{ route('mypage.index', ['page' => 'favorite']) }}"
            class="mypage__tab {{ request('page') === 'favorite' ? 'is-active' : '' }}">
            マイリスト
        </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="mypage__items">
        @foreach ($items as $item)
            <a href="{{ route('items.show', $item->id) }}" class="item-card">
                <div class="item-card__image">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="">
                </div>
                <p class="item-card__name">{{ $item->name }}</p>
            </a>
        @endforeach
    </div>

</div>
@endsection