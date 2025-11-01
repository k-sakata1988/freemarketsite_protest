<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreeMarket</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">

            @if (in_array(request()->route()->getName(), ['login', 'register']))
                <a class="header__logo" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="Coachtech Logo">
                </a>
            @else
                <div class="header-utilities">
                    <a class="header__logo" href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.svg') }}" alt="Coachtech Logo">
                    </a>
                    <form class="header__search-form" action="{{ route('items.index') }}" method="GET">
                        <input type="text" name="keyword" class="header__search-input" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                        <input type="hidden" name="tab" value="{{ request('tab') ?? (auth()->check() ? 'mylist' : 'recommended') }}">
                    </form>
                    <nav>
                        <ul class="header-nav">
                            @auth
                                <li class="header-nav__item">
                                    <form class="form" action="/logout" method="post">
                                        @csrf
                                        <button class="header-nav__button">ログアウト</button>
                                    </form>
                                </li>
                                <li class="header-nav__item">
                                    <a class="header-nav__link" href="/mypage">マイページ</a>
                                </li>
                                <li class="header-nav__item">
                                    <a class="header-nav__button--sell" href="/sell">出品</a>
                                </li>
                            @endauth

                            @guest
                                <li class="header-nav__item">
                                    <a class="header-nav__link" href="/login">ログイン</a>
                                </li>
                                <li class="header-nav__item">
                                    <a class="header-nav__link" href="/login">マイページ</a>
                                </li>
                                <li class="header-nav__item">
                                    <a class="header-nav__button--sell" href="/login">出品</a>
                                </li>
                            @endguest
                        </ul>
                    </nav>
                </div>
            @endif

        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
