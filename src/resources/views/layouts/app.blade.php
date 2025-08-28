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
        <nav class="header-nav">
            <ul class="header-nav__list">
                <li class="header-nav__item">
                    <a href="#" class="header-nav__link">Home</a>
                </li>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        @yield('content')
    </main>
</body>

</html>
