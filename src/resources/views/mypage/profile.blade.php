@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">

    {{-- タイトル --}}
    <h2 class="mypage__title">マイページ</h2>

    {{-- ユーザー情報 --}}
    <div class="mypage__profile">
        <img src="{{ asset('images/user-icon.png') }}" class="mypage__icon" alt="ユーザーアイコン">
        <p class="mypage__name">{{ Auth::user()->name ?? 'ゲスト' }}</p>
        <p class="mypage__email">{{ Auth::user()->email ?? '' }}</p>

        <a href="#" class="mypage__edit">プロフィールを編集</a>
    </div>

    {{-- タブメニュー --}}
    <ul class="mypage__tab-menu">
        <li class="tab {{ request('page') === 'sell' ? 'active' : '' }}">
            <a href="{{ route('mypage.index', ['page' => 'sell']) }}">出品した商品</a>
        </li>
        <li class="tab {{ request('page') === 'buy' ? 'active' : '' }}">
            <a href="{{ route('mypage.index', ['page' => 'buy']) }}">購入した商品</a>
        </li>
        <li class="tab {{ request('page') === 'like' ? 'active' : '' }}">
            <a href="{{ route('mypage.index', ['page' => 'like']) }}">マイリスト</a>
        </li>
    </ul>

    {{-- タブ内容 --}}
    <div class="mypage__tab-content">
        @if(request('page') === 'sell')
            @include('mypage.tabs.sell')
        @elseif(request('page') === 'buy')
            @include('mypage.tabs.buy')
        @elseif(request('page') === 'like')
            @include('mypage.tabs.like')
        @else
            <p>表示する項目を選択してください。</p>
        @endif
    </div>

</div>
@endsection