<header class="header" id="header">
    <div class="container">
        <div class="header__top">
            <a href="/" class="header__logo logo">
                <img src="/images/icons/logo.svg" alt="logo">
            </a>
            <ul class="header__contacts">
                <li>
                    <img src="/images/icons/location.svg" alt="loc">
                    <span>МО г. Домодедово, Белые Столбы,<br>улица Авенариуса, строение 6</span>
                </li>
                <li>Время работы:<br>9:00 - 18:00</li>
                <li>
                    <a class="header__phone phone" href="tel:+7 (499) 714-71-44">+7 (499) 714 71 44
                        <span>Заказать звонок</span></a>
                </li>
            </ul>
            <div class="header__shop-menu-wrapper">
                <ul class="header__shop-menu shop-menu">
                    <li class="shop-menu__item shop-menu__item-phone">
                        <a class="shop-menu__phone" href="#!">
                            <div class="shop-menu__item shop-menu__item-phone">
                                <svg width="28" height="30" viewBox="0 0 28 30" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M26.8654 21.4705L23.4373 17.8517C22.4922 16.8541 20.9544 16.8541 20.0093 17.8517L18.4511 19.4966C17.7352 20.2524 16.5701 20.2522 15.8544 19.4969L9.10055 12.3611C8.38289 11.6035 8.38278 10.3773 9.10055 9.61961C9.35153 9.35466 10.0722 8.59388 10.6587 7.97472C11.5997 6.98146 11.6128 5.36307 10.6578 4.35502L7.2307 0.74856C6.2856 -0.249084 4.74778 -0.249084 3.80521 0.745945C3.09988 1.48404 2.87239 1.72215 2.55613 2.05311C-0.852017 5.65087 -0.852017 11.5049 2.55597 15.1025L13.2572 26.4052C16.6733 30.0115 22.2024 30.0118 25.6188 26.4052L26.8654 25.0893C27.8105 24.0916 27.8105 22.4682 26.8654 21.4705ZM4.94539 1.95481C5.26041 1.62226 5.77292 1.6222 6.08896 1.95572L9.51607 5.56218C9.83184 5.89552 9.83184 6.43502 9.51607 6.76842L8.94469 7.37155L4.37697 2.54963L4.94539 1.95481ZM14.4 25.1991L3.69879 13.8963C1.062 11.1128 0.917064 6.74204 3.25306 3.77572L7.80752 8.58365C6.61079 10.0158 6.66067 12.1981 7.95763 13.5672L14.7112 20.7026C16.0068 22.0703 18.0741 22.1266 19.4324 20.8616L23.987 25.6696C21.186 28.1314 17.055 28.0018 14.4 25.1991ZM25.7228 23.883L25.1514 24.4862L20.5807 19.6611L21.152 19.058C21.467 18.7254 21.9796 18.7254 22.2947 19.058L25.7227 22.6768C26.0378 23.0094 26.0378 23.5505 25.7228 23.883Z"
                                        fill="white"/>
                                </svg>
                            </div>
                        </a>
                    </li>
                    <li class="shop-menu__item">
                        <a href="{{ route('personal.index') }}">
                            <button class="shop-menu__profile-btn">
                                <div class="shop-menu__item">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="16" cy="16" r="15" stroke="white" stroke-width="2"/>
                                        <circle cx="16.0004" cy="13.1557" r="4.68889" stroke="white" stroke-width="2"/>
                                        <path
                                            d="M27.3953 26.0251C25.8385 21.2443 21.5205 17.8066 16.4374 17.8066C11.3543 17.8066 7.03631 21.2443 5.47949 26.0251"
                                            stroke="white" stroke-width="2"/>
                                    </svg>
                                </div>
                            </button>
                        </a>
                    </li>
                    <li class="shop-menu__item">
                        <a href="{{ route('personal.favorites') }}" class="shop-menu__favorites-link">
                            <div class="shop-menu__item">
                                <favorite-icon-component></favorite-icon-component>
                            </div>
                        </a>
                    </li>
                    <li class="shop-menu__item">
                        <a href="{{ route('basket.index') }}" class="shop-menu__cart-link">
                            <div class="shop-menu__item">
                                <basket-icon-component></basket-icon-component>
                            </div>
                        </a>
                    </li>
                </ul><div class="shop-menu__links-container">
                    <a href="signin.html" class="shop-menu__links">Вход</a>
                    <span class="slash">/</span>
                    <a href="signup.html" class="shop-menu__links">Регистрация</a>
                </div>
            </div>
            <button class="mobile-nav-btn header__mobile-nav-btn">
                <div class="nav-icon"></div>
            </button>
        </div>
    </div>
    <div class="header__bottom">
        <div class="container">
            <div class="header__bottom-inner">
                <nav class="header__dropdown-menu dropdown-menu">
                    @include('blocks/dropdown-menu')
                </nav>
                <nav class="header__nav nav">
                    <ul class="nav__list">
                        <li><a href="{{ route('company.index') }}">О компании</a></li>
                        <li><a href="{{ route('actions.index') }}">Акции</a></li>
                        <li><a href="#!">3D проект</a></li>
                        <li>
                            <a href="{{ route('news.index') }}">
                                Новости
                            </a>
                        </li>
                    </ul>
                </nav>

                @include('blocks/social-icon')

                <form class="header__search-form search-form" action="{{ route('products.search') }}">
                    <button type="submit" class="search__form-btn"></button>
                    <input type="text" name="query" class="search-form-txt" placeholder="Поиск по каталогу">
                </form>
            </div>
        </div>
    </div>
</header>
@include('blocks/mobile-nav')
