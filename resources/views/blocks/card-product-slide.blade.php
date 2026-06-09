<div class="keen-slider__slide">
    <article class="card">
        <div class="card__picture">
            <div class="card__row-1">
                @if ($element->getTag())
                    <div class="card__status">
                        {{ $element->getTag() }}
                    </div>
                @endif
                <btn-favorite-product-component :product-id="{{ $element->id }}"></btn-favorite-product-component>
            </div>
            <img src="{{ $element->getImageUrlAttribute() }}" alt="card-img" loading="lazy" decoding="async">
        </div>
        <div class="card__desc">
            <div class="card__desc-row">
                <div class="card__title">
                    {{ $element->name }}
                </div>

            </div>
            <div class="card__row-2">
                <div class="card__price-container">
                    <span class="card__price">
                        {{ number_format($element->getCurrentPrice(), 0, '.', ' ') }} ₽
                    </span>
                    @if ($element->price > $element->getCurrentPrice())
                        <span class="card__price-old">
                            {{ number_format($element->price, 0, '.', ' ') }} ₽
                        </span>
                    @endif

                </div>

            </div>
            <btn-add-basket-component :product-id="{{ $element->id }}"></btn-add-basket-component>
        </div>
        <a href="{{ route('products.detail', ['slug' => $element->slug]) }}" class="card__link" aria-label="Открыть товар {{ $element->name }}"></a>
    </article>
</div>
