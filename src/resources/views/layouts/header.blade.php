<header class="header">
    <div class="header__inner">

        {{-- 左：ロゴ --}}
        <div class="header__logo">
            <img src="{{ asset('images/coachtech.png') }}" class="header__logo-img" alt="COACHTECH">
        </div>

        {{-- 中央：検索フォーム：ログイン画面&登録画面以外 --}}
        @if (!Request::is('login') && !Request::is('register'))
        <form action="/items" method="GET" class="header__search">
            <input type="text" name="keyword" placeholder="なにをお探しですか？" class="header__search-input">
        </form>
        @endif

        {{-- 右：メニュー --}}
        <div class="header__menu">
            @guest
                @if (!Request::is('login') && !Request::is('register'))
                    <a href="{{ route('login') }}">ログイン</a>
                    <a href="{{ route('mypage.profile') }}">マイページ</a>
                    {{-- <a href="{{ route('items.sell') }}" class="header__sell-button">出品</a> --}}
                @endif
            @else
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="header__logout-button">ログアウト</button>
                </form>
                <a href="{{ route('mypage.profile') }}">マイページ</a>
                {{-- <a href="{{ route('items.sell') }}" class="header__sell-button">出品</a> --}}
            @endguest
        </div>
    </div>
</header>