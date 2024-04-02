<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>BanyaStore</title>
    <link rel="stylesheet" href="/css/main.css"/>
    <link rel="stylesheet" href="/fonts/raleway/stylesheet.css"/>
</head>
<body>

@include('blocks/header')

<div class="main">
    @yield('content')
</div>

@include('blocks/footer')

<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/app.js') }}"></script>

</body>
</html>
