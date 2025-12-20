<header class="header">
    <div class="header__inner">

        {{-- 左：ロゴ --}}
        <div class="header__logo">
            <img src="{{ asset('images/coachtech.png') }}" class="header__logo-img" alt="COACHTECH">
        </div>

        {{-- 中央：検索フォーム：ログイン画面&登録画面以外 --}}
        @if (!Request::is('login') && !Request::is('register'))
            <form action="{{ Auth()->check() ? route('mypage.index') : route('items.index') }}" method="GET" class="header__search">
                <input type="hidden" name="page" value="{{ request('page', 'recommend') }}">
                <input type="text" name="keyword" placeholder="なにをお探しですか？" class="header__search-input" value="{{ request('keyword') }}">
            </form>
        @endif

        {{-- 右：メニュー --}}
        @if (!Request::is('login') && !Request::is('register'))
            <div class="header__menu">
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="header__logout-button">ログアウト</button>
                    </form>
                    <a href="{{ route('mypage.index') }}">マイページ</a>
                    <a href="{{ route('items.item-sell') }}" class="header__sell-button">出品</a>
                @endauth
                @guest
                    <a href="{{ route('login') }}">ログイン</a>
                    <span class="header__link is-disabled">マイページ</span>
                    <span class="header__sell-button is-disabled">出品</span>
                @endguest
            </div>
        @endif
    </div>
</header>