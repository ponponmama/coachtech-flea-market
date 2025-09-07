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
    <header class="header-container">
        <div class="header-logo">
            <img class="header-logo-image" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
        </div>

        {{-- 常に検索フォームを表示（未認証ユーザーでも使用可能） --}}
        <div class="header-search">
            <form class="search-form" action="#" method="GET">
                <input type="text" class="search-input" placeholder="なにをお探しですか?" name="search">
            </form>
        </div>

        {{-- ナビゲーションメニュー --}}
        <nav class="header-nav">
            <ul class="header-nav__list">
                @auth
                    {{-- ログイン済みユーザー用メニュー --}}
                    <li class="header-nav__item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="header-nav__link button">ログアウト</button>
                        </form>
                    </li>
                    <li class="header-nav__item">
                        <a href="{{ route('mypage') }}" class="header-nav__link link">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a href="{{ route('sell') }}" class="header-nav__link sell-link link">出品</a>
                    </li>
                @else
                    {{-- 未ログインユーザー用メニュー --}}
                    <li class="header-nav__item">
                        <a href="{{ route('login') }}" class="header-nav__link link">ログイン</a>
                    </li>
                    <li class="header-nav__item">
                        <a href="{{ route('register') }}" class="header-nav__link link">会員登録</a>
                    </li>
                @endauth
            </ul>
        </nav>
    </header>
    <main class="main-content">
        @yield('content')
    </main>
    @yield('js')
</body>

</html>
