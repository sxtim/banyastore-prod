<nav class="lk__nav">
    <ul class="lk__nav-item">
        <a class="btn-lk-nav btn {{ $activeMenu == 'personal' ? 'btn-lk-nav-active' : '' }}" href="{{ route('personal.index') }}">Личные данные</a>
    </ul>
    <ul class="lk__nav-item">
        <a class="btn-lk-nav btn {{ $activeMenu == "orders" ? 'btn-lk-nav-active' : '' }}" href="{{ route('personal.orders') }}">Заказы</a>
    </ul>
    <ul class="lk__nav-item">
        <a class="btn-lk-nav btn" href="{{ route('personal.favorites') }}">Избранное</a>
    </ul>
    <ul class="lk__nav-item">
        <a class="btn-lk-nav btn" href="{{ route('basket.index') }}">Корзина</a>
    </ul>
</nav>
