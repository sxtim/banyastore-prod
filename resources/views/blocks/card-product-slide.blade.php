<div class="keen-slider__slide">
    <article class="card">
        <div class="card__picture">
            <div class="card__row-1">
                @if ($product->getTag())
                    <div class="card__status">
                        {{ $product->getTag() }}
                    </div>
                @endif
                <btn-favorite-product-component :product-id="{{ $product->id }}"></btn-favorite-product-component>
            </div>
            <img src="{{ $product->getImageUrlAttribute() }}" alt="card-img">
        </div>
        <div class="card__desc">
            <div class="card__desc-row">
                <div class="card__title">
                    {{ $product->name }}
                </div>

            </div>
            <div class="card__row-2">
                <div class="card__price-container">
                    <span class="card__price">
                        {{ number_format($product->getCurrentPrice(), 0, '.', ' ') }} ₽
                    </span>
                    @if ($product->price > $product->getCurrentPrice())
                        <span class="card__price-old">
                            {{ number_format($product->price, 0, '.', ' ') }} ₽
                        </span>
                    @endif

                </div>

            </div>
            <btn-add-basket-component :product-id="{{ $product->id }}"></btn-add-basket-component>
        </div>
        <a href="{{ route('products.detail', ['slug' => $product->slug]) }}" class="card__link"></a>
    </article>
</div>
