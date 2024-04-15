<article class="card">
    <div class="card__picture">
        <div class="card__row-1">
            @if ($product->getTag())
                <div class="card__status">
                    {{ $product->getTag() }}
                </div>
            @endif
            <div class="card__fav">
                <button class="card__btn-add-to-fav">
                    <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill="transparent"
                              d="M20.8413 3.55044C20.3305 3.03944 19.7241 2.63409 19.0566 2.35752C18.3892 2.08096 17.6738 1.93861 16.9513 1.93861C16.2288 1.93861 15.5134 2.08096 14.8459 2.35752C14.1785 2.63409 13.572 3.03944 13.0613 3.55044L12.0013 4.61044L10.9413 3.55044C9.90959 2.51875 8.51031 1.93915 7.05128 1.93915C5.59225 1.93915 4.19297 2.51875 3.16128 3.55044C2.12959 4.58213 1.54999 5.98141 1.54999 7.44044C1.54999 8.89947 2.12959 10.2987 3.16128 11.3304L4.22128 12.3904L12.0013 20.1704L19.7813 12.3904L20.8413 11.3304C21.3523 10.8197 21.7576 10.2133 22.0342 9.54579C22.3108 8.87834 22.4531 8.16293 22.4531 7.44044C22.4531 6.71795 22.3108 6.00254 22.0342 5.33508C21.7576 4.66763 21.3523 4.0612 20.8413 3.55044V3.55044Z"
                              stroke="#DEDBDB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
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
               {{ $product->getCurrentPrice() }}
          </span>
                @if ($product->price > $product->getCurrentPrice())
                    <span class="card__price-old">
                        {{ $product->price }}
                    </span>
                @endif

                <div class="card__in-stock">
                    <!--          <div class="card__tick">&#10004</div>-->
                    <!--          <div class="card__tick">&times</div>-->

                    {{--          <span>@@inStock</span>--}}
                </div>

            </div>
            <btn-add-basket-component :product-id="{{ $product->id }}"></btn-add-basket-component>
        </div>
    </div>
    <a href="{{ route('products.detail', ['slug' => $product->slug]) }}" class="card__link"></a>
</article>
