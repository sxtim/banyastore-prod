<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('pagetitle')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ url('/') }}" />
    <link rel="stylesheet" href="./styles/backend/font-awesome.min.css" />
    <link href="./styles/backend/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=cyrillic" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300&display=swap&subset=cyrillic" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;600;700;900&display=swap" rel="stylesheet">
{{--    <link rel="stylesheet" href="./styles/old/theme.min.css">--}}
{{--    <link href="./styles/old/summernote-bs4.min.css" rel="stylesheet">--}}
{{--    <link href="./styles/old/datetimepicker.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="./styles/backend.css">
{{--    <link rel="stylesheet" href="{{ mix('css/backend/app.css') }}">--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />--}}
</head>
<body>

<header>
    <div class="main-box">
        <div class="header-vn-box">
            <a href="{{ route('backend.index') }}" class="logo">
                <img src="../img/logo-b.png" alt="" style="width: 130px;">
            </a>
            <div class="logout">
                <a href="/" class="back-lc">
                    На сайт
                </a>
            </div>
            <a href="/" class="back-lc" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">Выйти из аккаунта</a>
            <form id="frm-logout" action="/" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</header>

<div class="nav nav--modif mb-4">
    <div class="main-box d-flex justify-content-between">
        <div class="links flex-wrap">
            <a href="{{ route('backend.index') }}" class="@yield('aside_index')">Информация</a>
            <a href="{{ route('backend.banner.index') }}" class="@yield('aside_banner')">Баннеры</a>
            <a href="{{ route('backend.order.index') }}" class="@yield('aside_orders')">Заказы</a>
            <a href="{{ route('backend.shop.index') }}" class="@yield('aside_shop')">Интернет-магазин</a>
            <a href="{{ route('backend.actions.index') }}" class="@yield('aside_actions')">Акции</a>
            <a href="{{ route('backend.news.index') }}" class="@yield('aside_news')">Новости</a>
            <a href="{{ route('backend.users.index') }}" class="@yield('aside_users')">Пользователи</a>
            <a href="{{ route('backend.feedback.index') }}" class="@yield('aside_feedback')">Обратная связь</a>
        </div>

    </div>
</div>

<div class="main-box">
    @yield('content')
</div>


<script src="{{ mix('js/backend/manifest.js') }}"></script>
<script src="{{ mix('js/backend/vendor.js') }}"></script>
<script src="{{ mix('js/backend/app.js') }}"></script>
@yield('scripts')

</body>
</html>
