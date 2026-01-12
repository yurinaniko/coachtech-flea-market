<header class="header">
    <div class="header__inner">
        <div class="header__logo">
            <img src="{{ asset('images/coachtech.png') }}" class="header__logo-img" alt="COACHTECH">
        </div>
        @if (!Request::is('login') && !Request::is('register'))
            <form action="{{ Auth()->check() ? route('mypage.index') : route('items.index') }}" method="GET" class="header__search">
                <input type="hidden" name="page" value="{{ request('page', 'recommend') }}">
                <input type="text" name="keyword" placeholder="なにをお探しですか？" class="header__search-input" value="{{ request('keyword') }}">
            </form>
        @endif
        @php
            $hideHeaderMenu =
                Request::is('login')
                || Request::is('register')
                || Request::is('email/verify');
        @endphp
        @if (! $hideHeaderMenu)
            <div class="header__menu">
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="header__logout-button">ログアウト</button>
                    </form>
                    <a href="{{ route('mypage.profile') }}" class="header__link">マイページ</a>
                    <a href="{{ route('items.item-sell') }}" class="header__sell-button">出品</a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="header__link">ログイン</a>
                    <span class="header__link is-disabled" aria-disabled="true">マイページ</span>
                    <span class="header__sell-button is-disabled" aria-disabled="true">出品</span>
                @endguest
            </div>
        @endif
    </div>
</header>