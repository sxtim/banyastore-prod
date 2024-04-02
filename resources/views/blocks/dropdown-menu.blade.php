<ul class="menu clearfix">
  <li class="parent">
    <div class="dropdown-menu__title" href="">
        <img src="/images/icons/catalog-burger.svg" alt="cat">
        <span>Каталог</span>
    </div>
    <ul class="children">
        @foreach($menu as $itemMenu)
            <li>
                <a href="{{ route('products.by-category', ['slug' => $itemMenu->slug]) }}">
                    {{ $itemMenu->name }}
                </a>
            </li>
        @endforeach
    </ul>
  </li>
</ul>
