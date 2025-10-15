<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body class="body-container">
    <header class="header-container auth-header">
        <div class="header-logo-container">
            <img class="header-logo" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
        </div>
        @unless (request()->routeIs('verification.notice') || request()->routeIs('login') || request()->routeIs('register'))
            {{-- 認証関連ページ以外では検索フォームを表示 --}}
            <div class="header-search">
                <form class="search-form" action="{{ route('top') }}" method="GET">
                    <input type="text" class="search-input" placeholder="なにをお探しですか?" name="search" value="{{ request('search') }}">
                </form>
            </div>
            {{-- ナビゲーションメニュー --}}
            <nav class="header-nav">
                <ul class="header-nav__list">
                    {{-- ログインボタン（常に表示） --}}
                    <li class="header-nav__item">
                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="header-nav__link button">ログアウト</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="header-nav__link link">ログイン</a>
                        @endauth
                    </li>

                    {{-- マイページボタン（常に表示） --}}
                    <li class="header-nav__item">
                        @auth
                            <a href="{{ route('mypage') }}" class="header-nav__link link">マイページ</a>
                        @else
                            <a href="{{ route('login') }}" class="header-nav__link link">マイページ</a>
                        @endauth
                    </li>

                    {{-- 出品ボタン（常に表示） --}}
                    <li class="header-nav__item sell-link-container">
                        @auth
                            <a href="{{ route('sell') }}" class="header-nav__link sell-link link">出品</a>
                        @else
                            <a href="{{ route('login') }}" class="header-nav__link sell-link link">出品</a>
                        @endauth
                    </li>
                </ul>
            </nav>
        @endunless
    </header>
    <main class="main-content">
        @yield('content')
    </main>
    @yield('js')
</body>

</html>
