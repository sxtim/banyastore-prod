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
                    <a class="header__phone phone" href="{{ route('feedback-form') }}">+7 (499) 714 71 44
                        <span>Заказать звонок</span></a>
                </li>
            </ul>
            <div class="header__shop-menu-wrapper">
                <ul class="header__shop-menu shop-menu">
                    <li class="shop-menu__item shop-menu__item-phone">
                        <a class="shop-menu__phone" href="{{ route('feedback-form') }}">
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
                            <button type="button" class="shop-menu__profile-btn" aria-label="Личный кабинет">
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
                        <a href="{{ route('personal.favorites') }}" class="shop-menu__favorites-link" aria-label="Избранное">
                            <div class="shop-menu__item">
                                <favorite-icon-component></favorite-icon-component>
                            </div>
                        </a>
                    </li>
                    <li class="shop-menu__item">
                        <a href="{{ route('basket.index') }}" class="shop-menu__cart-link" aria-label="Корзина">
                            <div class="shop-menu__item">
                                <basket-icon-component></basket-icon-component>
                            </div>
                        </a>
                    </li>
                </ul>
                @auth
                    <div class="shop-menu__links-container">
                        <span class="shop-menu__links">{{ Auth::user()->getFullName() }}</span>
                        <span class="slash">/</span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="shop-menu__links">Выйти</button>
                        </form>
                    </div>
                @endauth
                @guest
                <div class="shop-menu__links-container">
                    <a href="{{ route('login') }}" class="shop-menu__links">Вход</a>
                    <span class="slash">/</span>
                    <a href="{{ route('register') }}" class="shop-menu__links">Регистрация</a>
                </div>
                @endguest
            </div>
            <button type="button" id="mobile-nav-btn" class="mobile-nav-btn header__mobile-nav-btn" aria-label="Открыть меню" aria-controls="mob-nav-js" aria-expanded="false">
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
                        <li><a href="{{ route('three-d-projects') }}">3D проект</a></li>
                        <li>
                            <a href="{{ route('news.index') }}">
                                Новости
                            </a>
                        </li>
                    </ul>
                </nav>

                @include('blocks/social-icon')

                <form class="header__search-form search-form" action="{{ route('products.search') }}">
                    <button type="submit" class="search__form-btn" aria-label="Найти"></button>
                    <input type="text" name="query" class="search-form-txt" placeholder="Поиск по каталогу">
                </form>
            </div>
        </div>
    </div>
    <div class="btn-contact-us__wrap">
        <ul class="btn-contact-us__list">
            <li>
                Как вам удобнее с нами связаться?
            </li>
            <li>
            <a href="https://t.me/+79771447736">
               <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 455.731 455.731" style="enable-background:new 0 0 455.731 455.731;" xml:space="preserve"><g><rect x="0" y="0" style="fill: rgb(24, 135, 220);" width="455.731" height="455.731" fill="#61A8DE"></rect><path style="" d="M358.844,100.6L54.091,219.359c-9.871,3.847-9.273,18.012,0.888,21.012l77.441,22.868l28.901,91.706 c3.019,9.579,15.158,12.483,22.185,5.308l40.039-40.882l78.56,57.665c9.614,7.057,23.306,1.814,25.747-9.859l52.031-248.76 C382.431,106.232,370.443,96.08,358.844,100.6z M320.636,155.806L179.08,280.984c-1.411,1.248-2.309,2.975-2.519,4.847 l-5.45,48.448c-0.178,1.58-2.389,1.789-2.861,0.271l-22.423-72.253c-1.027-3.308,0.312-6.892,3.255-8.717l167.163-103.676 C320.089,147.518,324.025,152.81,320.636,155.806z" fill="#FFFFFF"></path></g></svg>Напишите нам в Telegram
               </a>
            </li>
            <li>
            <a href="https://wa.me/+79771447736">
                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 455.731 455.731" style="enable-background:new 0 0 455.731 455.731;" xml:space="preserve"><g><rect x="0" y="0" style="fill:#1BD741;" width="455.731" height="455.731"/><g><path style="fill:#FFFFFF;" d="M68.494,387.41l22.323-79.284c-14.355-24.387-21.913-52.134-21.913-80.638 c0-87.765,71.402-159.167,159.167-159.167s159.166,71.402,159.166,159.167c0,87.765-71.401,159.167-159.166,159.167 c-27.347,0-54.125-7-77.814-20.292L68.494,387.41z M154.437,337.406l4.872,2.975c20.654,12.609,44.432,19.274,68.762,19.274 c72.877,0,132.166-59.29,132.166-132.167S300.948,95.321,228.071,95.321S95.904,154.611,95.904,227.488 c0,25.393,7.217,50.052,20.869,71.311l3.281,5.109l-12.855,45.658L154.437,337.406z"/><path style="fill:#FFFFFF;" d="M183.359,153.407l-10.328-0.563c-3.244-0.177-6.426,0.907-8.878,3.037 c-5.007,4.348-13.013,12.754-15.472,23.708c-3.667,16.333,2,36.333,16.667,56.333c14.667,20,42,52,90.333,65.667 c15.575,4.404,27.827,1.435,37.28-4.612c7.487-4.789,12.648-12.476,14.508-21.166l1.649-7.702c0.524-2.448-0.719-4.932-2.993-5.98 l-34.905-16.089c-2.266-1.044-4.953-0.384-6.477,1.591l-13.703,17.764c-1.035,1.342-2.807,1.874-4.407,1.312 c-9.384-3.298-40.818-16.463-58.066-49.687c-0.748-1.441-0.562-3.19,0.499-4.419l13.096-15.15 c1.338-1.547,1.676-3.722,0.872-5.602l-15.046-35.201C187.187,154.774,185.392,153.518,183.359,153.407z"/></g></g></svg>Напишите нам в Whatsapp
                </a>
            </li>
        </ul>
        <div class="btn-contact-us__btn">
            <svg class="send" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 612 612" style="enable-background:new 0 0 612 612;" xml:space="preserve"><g><g><path d="M535.04,0H76.961C35.939,0,2.564,33.375,2.564,74.399v300.491c0,41.023,33.375,74.399,74.399,74.399h250.863v144.555 c0,7.58,4.71,14.361,11.811,17.011c2.07,0.772,4.215,1.144,6.339,1.144c5.167,0,10.209-2.207,13.727-6.268l135.52-156.442h39.815 c41.023,0,74.398-33.375,74.398-74.399V74.399C609.438,33.375,576.063,0,535.04,0z M573.128,374.891 c0,21.001-17.085,38.089-38.088,38.089c0,0-46.972,0.012-47.248,0c-5.366-0.251-10.792,1.891-14.583,6.268L364.138,545.161 V431.137c0-10.026-8.129-18.155-18.155-18.155H76.961c-21.002,0-38.089-17.088-38.089-38.089V74.399 C38.874,53.398,55.959,36.31,76.961,36.31h458.077c21.002,0,38.088,17.088,38.088,38.089v300.491H573.128z" fill="#000000" style="fill: rgb(255, 255, 255);"></path><path d="M509.29,119.751H102.713c-10.026,0-18.155,8.129-18.155,18.155s8.129,18.155,18.155,18.155H509.29 c10.026,0,18.155-8.129,18.155-18.155S519.318,119.751,509.29,119.751z" fill="#000000" style="fill: rgb(255, 255, 255);"></path><path d="M509.29,206.489H102.713c-10.026,0-18.155,8.129-18.155,18.155c0,10.026,8.129,18.155,18.155,18.155H509.29 c10.026,0,18.155-8.129,18.155-18.155C527.445,214.618,519.318,206.489,509.29,206.489z" fill="#000000" style="fill: rgb(255, 255, 255);"></path><path d="M295.159,293.225H102.713c-10.026,0-18.155,8.129-18.155,18.155s8.129,18.155,18.155,18.155h192.448 c10.026,0,18.155-8.129,18.155-18.155C313.314,301.355,305.187,293.225,295.159,293.225z" fill="#000000" style="fill: rgb(255, 255, 255);"></path></g></g></svg>
           
             
            <svg class= "close" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7.293 16.707a1 1 0 0 0 1.414 0l3.293-3.293 3.293 3.293a1 1 0 0 0 1.414-1.414l-3.293-3.293 3.293-3.293a1 1 0 1 0 -1.414-1.414l-3.293 3.293-3.293-3.293a1 1 0 1 0 -1.414 1.414l3.293 3.293-3.293 3.293a1 1 0 0 0 0 1.414z" fill="#000000" style="fill: rgb(255, 255, 255);"></path></svg>
        </div>
    </div>
</header>
@include('blocks/mobile-nav')
