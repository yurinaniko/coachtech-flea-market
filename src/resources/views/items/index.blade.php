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
        <button class="tab active">おすすめ</button>
        <button class="tab">マイリスト</button>
    </div>
</div>
<div class="item-list">
    {{-- 商品カード一覧（ダミーデータ8個） --}}
    <div class="item-list__grid">
        @foreach ($items as $item)
            <a href="{{ route('items.show', $item->id) }}" class="item-card__link">
                <div class="item-card">
                    <div class="item-card__image">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-content">
                    </div>
                    <p class="item-card__name">{{ $item->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection