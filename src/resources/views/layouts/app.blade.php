<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

        @auth
            {{-- メール認証が完了している場合のみナビゲーションを表示 --}}
            @if (auth()->user()->email_verified_at)
                <div class="header-search">
                    <form class="search-form" action="#" method="GET">
                        <input type="text" class="search-input" placeholder="なにをお探しですか?" name="search">
                    </form>
                </div>
                <nav class="header-nav">
                    <ul class="header-nav__list">
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
                    </ul>
                </nav>
            @endif
        @endauth
    </header>
    <main class="main-content">
        @yield('content')
    </main>
    @yield('js')
</body>

</html>
