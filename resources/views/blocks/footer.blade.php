<footer class="footer">
  <div class="container">
    <div class="footer__wrapper">
      <div class="footer__col">
        <div class="footer__title">Контакты</div>
        <ul>
          <li><a href="/">г. Домодедово, <br> Белые Столбы, <br> ул Авенариуса, стр 6</a></li>
          <li><a class="footer__list-text" href="mailto:opt@feringermsk.ru">opt@feringermsk.ru</a></li>
          <li><a class="footer__phone phone" href="tel:+7 (499) 714-71-44">+7 (499) 714 71 44 </a></li>
          <li><a class="footer__phone phone" href="tel:+7 (977) 144-77-36">+7 (977) 144 77 36 </a></li>
          <li class="footer__social">@include('blocks/social-icon')</li>
        </ul>
      </div>
      <div class="footer__col">
        <div class="footer__title">Каталог товаров</div>
        <ul>
            @foreach($menu as $itemMenu)
                @if ($itemMenu->parent_id === null)
                    <li>
                        <a href="{{ route('products.by-category', ['slug' => $itemMenu->slug]) }}">
                            {{ $itemMenu->name }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
      </div>
      <div class="footer__col">
        <div class="footer__title">Информация</div>
        <ul>
          <li><a href="{{ route('company.index') }}">О компании</a></li>
          <li><a href="/">Доставка</a></li>
          <li><a href="/">Гарантия</a></li>
          <li><a href="{{ route('company.index') }}">Реквизиты</a></li>
          <li class="footer__cr"><a href="/">2024 © Banyastore</a></li>
        </ul>
          <iframe
              src="https://yandex.ru/sprav/widget/rating-badge/216236942469?type=rating&theme=dark"
              width="150"
              height="50"
              frameborder="0"
              sandbox="allow-scripts allow-same-origin allow-popups"
              referrerpolicy="no-referrer"
          >
          </iframe>
      </div>
    </div>
  </div>
</footer>
