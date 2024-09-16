@extends('layouts.app')

@section('pagetitle', $seo->getTitle())
@section('description', $seo->getDescription())

@section('content')
    {{ Breadcrumbs::render('product', $product) }}
    <section class="product-detail section">
        <div class="container">
            <div class="product-detail__content">
                <div class="product-detail__logo">
                    <img src="" alt="">
                </div>
                <div class="product-detail__slider-wrapper">
                    <div id="product-detail-keen-slider" class="keen-slider">
                        <div class="keen-slider__slide product-detail__slider-slide">
                            <img class="product-detail__slider-img" src="{{ $product->getImageUrlAttribute() }}" alt="slide">
                        </div>
                        @foreach($product->additionalImages as $image)
                            <div class="keen-slider__slide product-detail__slider-slide">
                                <img src="{{ $image->getImageUrlAttribute() }}" alt="slide">
                            </div>
                        @endforeach
                    </div>
                    <div id="product-detail-thumbnails" class="keen-slider product-detail__thumbnails">
                        <div class="keen-slider__slide product-detail__slider-thumb">
                            <img src="{{ $product->getImageUrlAttribute() }}" alt="slide">
                        </div>
                        @foreach($product->additionalImages as $image)
                            <div class="keen-slider__slide product-detail__slider-thumb">
                                <img src="{{ $image->getImageUrlAttribute() }}" alt="slide">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="product-detail__desc">
                    <h1 class="product-detail__title title-s">
                        {{ $product->name }}
                    </h1>

                    @if ($product->preview_text && isset($product->preview_text['blocks']))
                        @foreach($product->preview_text['blocks'] as $block)
                            @if ($block['type'] == 'header' && isset($block['data']['level']))
                                <h{{ $block['data']['level'] }} >
                                    {!! $block['data']['text'] !!}
                                </h{{ $block['data']['level'] }}>
                            @endif

                            @if ($block['type'] == 'paragraph')
                                <p>
                                    {!! $block['data']['text'] !!}
                                </p>
                            @endif

                            @if ($block['type'] == 'image' && isset($block['data']['file']['url']))
                                <figure class="figure my-4" style="text-align: center">
                                    @if(isset($block['data']['link']) && $block['data']['link'])
                                        <a href="{{ $block['data']['link'] }}" target="_blank">
                                            <img loading="lazy" src="{{ $block['data']['file']['url'] }}"
                                                 class="figure-img img-fluid rounded"
                                                 width="825"
                                                {{--                                 alt="{% if altExists %} {{ block.data.caption|raw|split('|')|last }}{% endif %}"--}}
                                            >
                                        </a>
                                    @else
                                        <img loading="lazy" src="{{ $block['data']['file']['url'] }}"
                                             class="figure-img img-fluid rounded"
                                             width="825"
                                            {{--                         alt="{% if altExists %}{{ block.data.caption|raw|split('|')|last }}{% endif %}"--}}
                                        >
                                    @endif

                                    <figcaption class="figure-caption text-center">
                                        {{ $block['data']['caption'] }}
                                    </figcaption>
                                </figure>
                            @endif
                        @endforeach
                    @endif

                    <div class="product-detail__content-row-bottom">
                        <div class="product-detail__price-container">
                            <span class="product-detail__price">{{ number_format($product->getCurrentPrice(), 0, '.', ' ') }} ₽ </span>
                            @if ($product->price > $product->getCurrentPrice())
                                <span class="product-detail__price-old">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                            @endif
                        </div>
                        <div class="product-detail__btns-container" id="catalog">
                            <btn-detail-add-basket-component
                                :product-id="{{ $product->id }}"></btn-detail-add-basket-component>
                            <btn-detail-favorite-product-component
                                :product-id="{{ $product->id }}"></btn-detail-favorite-product-component>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-detail__tabs">
                <div data-tab-component>
                    <div role="tablist" aria-label="Tabbed content">
                        <button role="tab"
                                aria-selected="true"
                                aria-controls="tab1-content"
                                id="tab1">
                            <h3>Описание</h3>
                        </button>

                        <button role="tab"
                                aria-selected="false"
                                aria-controls="tab2-content"
                                id="tab2">
                            <h3>Характеристики</h3>
                        </button>

                        <button role="tab"
                                aria-selected="false"
                                aria-controls="tab3-content"
                                id="tab3">
                            <h3>Доставка</h3>
                        </button>

                        <button role="tab"
                                aria-selected="false"
                                aria-controls="tab4-content"
                                id="tab4">
                            <h3>Отзывы</h3>
                        </button>
                    </div>

                    <div id="tab1-content"
                         role="tabpanel"
                         aria-labelledby="tab1"
                         tabindex="0">

                        @if ($product->description && isset($product->description['blocks']))
                            @foreach($product->description['blocks'] as $block)
                                @if ($block['type'] == 'header' && isset($block['data']['level']))
                                    <h{{ $block['data']['level'] }} >
                                        {!! $block['data']['text'] !!}
                                    </h{{ $block['data']['level'] }}>
                                @endif

                                @if ($block['type'] == 'paragraph')
                                    <p>
                                        {!! $block['data']['text'] !!}
                                    </p>
                                @endif

                                @if ($block['type'] == 'image' && isset($block['data']['file']['url']))
                                    <figure class="figure my-4" style="text-align: center">
                                        @if(isset($block['data']['link']) && $block['data']['link'])
                                            <a href="{{ $block['data']['link'] }}" target="_blank">
                                                <img loading="lazy" src="{{ $block['data']['file']['url'] }}"
                                                     class="figure-img img-fluid rounded"
                                                     width="825"
                                                    {{--                                 alt="{% if altExists %} {{ block.data.caption|raw|split('|')|last }}{% endif %}"--}}
                                                >
                                            </a>
                                        @else
                                            <img loading="lazy" src="{{ $block['data']['file']['url'] }}"
                                                 class="figure-img img-fluid rounded"
                                                 width="825"
                                                {{--                         alt="{% if altExists %}{{ block.data.caption|raw|split('|')|last }}{% endif %}"--}}
                                            >
                                        @endif

                                        <figcaption class="figure-caption text-center">
                                            {{ $block['data']['caption'] }}
                                        </figcaption>
                                    </figure>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="product-detail__params" id="tab2-content"
                         role="tabpanel"
                         aria-labelledby="tab2"
                         tabindex="0"
                         aria-hidden="true">
                        @php
                            $i = 0;
                        @endphp
                        @foreach($product->propertiesValues as $value)
                            @php
                                $i++;
                            @endphp
                            @if ($i === 1)
                                <table class="product-params__table">
                                    @endif

                                    <tr class="product-params__row">
                                        <td class="product-params__cell-title">
                                            <span class="product-params__cell-decor">
                                                <span>{{ $value->property->name }}</span>
                                            </span>
                                        </td>
                                        <td class="product-params__cell-value">
                                            {{ $value->name }}
                                        </td>
                                    </tr>

                                    @if ($i === 4)
                                        @php
                                            $i = 0;
                                        @endphp
                                </table>
                                @endif
                                @endforeach

                                @if ($i != 4)
                                    </table>
                            @endif
                    </div>

                    <div class="product-detail__delivery" id="tab3-content"
                         role="tabpanel"
                         aria-labelledby="tab3"
                         tabindex="0"
                         aria-hidden="true">
                        <p>Возможные способы оплаты и доставки заказа определяются в зависимости от количества и
                            наименований
                            товаров в корзине, а также региона доставки. Стартовая цена доставки рассчитывается в момент
                            оформления
                            заказа и зависит от выбранного способа доставки и адреса доставки, а также от общей суммы
                            заказа.
                        <p> </p>
                        <p>
                            География поставок и развитая транспортная инфраструктура позволяет осуществить доставку
                            товаров нашего
                            интернет - магазина практически в любой регион РФ и страны СНГ.</p>
                        <ul class="product-detail__delivery-list delivery-list">
                            <li class="delivery-list__item"><img src="/images/product-detail/delivery-ic-01.svg"
                                                                 alt="ic">
                                <p>Самовывоз из пункта выдачи:
                                    <br> МО, г. Домодедово, мкр. Белые столбы, ул. Авенариуса С 6</p></li>
                            <li class="delivery-list__item"><img src="/images/product-detail/delivery-ic-02.svg"
                                                                 alt="ic">
                                <p>Доставка ТК до терминала:
                                    <br> СДЭК, ПЭК, Байкал - сервис или Деловые линии.</p></li>
                            <li class="delivery-list__item"><img src="/images/product-detail/delivery-ic-03.svg"
                                                                 alt="ic">
                                <p>Адресная доставка
                                    <br> транспортной компанией или курьером</p></li>
                        </ul>
                    </div>

                    <div class="product-detail__comments" id="tab4-content"
                         role="tabpanel"
                         aria-labelledby="tab4"
                         tabindex="0"
                         aria-hidden="true">
                        Раздел в разработке

                        {{--                        @include('blocks/card-comment')--}}
                        {{--                        @include('blocks/card-comment')--}}
                        {{--                        @include('blocks/card-comment')--}}
                        {{--                        <article class="card-comment">--}}
                        {{--                            <div class="card-comment__desc">--}}
                        {{--                                <div class="card-comment__name">Федор Лейкин </div>--}}
                        {{--                                <time class="card-comment__date">08.11.2023</time>--}}
                        {{--                                <div class="card-comment__message">Печь просто бомба. Паримся всей семьей, отличный режим русской бани. А еще она красивая и не обжигает за счет облицовки камнем.</div>--}}

                        {{--                            </div>--}}
                        {{--                            <a href="#!" class="card-comment__link">ещё</a>--}}
                        {{--                        </article>--}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
