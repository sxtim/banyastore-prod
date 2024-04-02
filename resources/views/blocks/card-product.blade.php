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
            <div class="card__btn">
                <button class="card__add-to-cart">
                    <svg width="60" height="56" viewBox="0 0 60 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path id="basket-background" d="M0 5C0 2.23858 2.23858 0 5 0H60V51C60 53.7614 57.7614 56 55 56H0V5Z"
                              fill="transparent"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M16 16C16 15.4477 16.4477 15 17 15H21.7273C22.2088 15 22.6219 15.3431 22.7103 15.8164L23.7405 21.3332H43C43.2959 21.3332 43.5765 21.4642 43.7665 21.691C43.9565 21.9178 44.0364 22.2171 43.9845 22.5084L42.0924 33.1425C41.9446 33.9397 41.5376 34.6697 40.9261 35.1972C40.3158 35.7238 39.5413 36.0133 38.7361 35.9995H27.2676C26.4624 36.0133 25.6878 35.7238 25.0775 35.1972C24.4662 34.6698 24.0592 33.94 23.9114 33.143C23.9113 33.1428 23.9114 33.1431 23.9114 33.143L21.9355 22.5625C21.9286 22.5331 21.923 22.5032 21.9188 22.4728L20.8967 17H17C16.4477 17 16 16.5523 16 16ZM24.114 23.3332L25.8778 32.778C25.9462 33.1471 26.1312 33.4648 26.384 33.6829C26.6351 33.8996 26.9376 34.0059 27.2376 33.9997L27.2582 33.9995H38.7455L38.766 33.9997C39.0661 34.0059 39.3686 33.8996 39.6197 33.6829C39.8717 33.4654 40.0564 33.1488 40.1253 32.7811C40.1255 32.7801 40.1256 32.7791 40.1258 32.778L41.8064 23.3332H24.114Z"
                              fill="#397345"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M37 42C37 40.8954 37.8954 40 39 40C40.1046 40 41 40.8954 41 42C41 43.1046 40.1046 44 39 44C37.8954 44 37 43.1046 37 42Z"
                              fill="#397345"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M25 42C25 40.8954 25.8954 40 27 40C28.1046 40 29 40.8954 29 42C29 43.1046 28.1046 44 27 44C25.8954 44 25 43.1046 25 42Z"
                              fill="#397345"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M59 1H5C2.79086 1 1 2.79086 1 5V55H55C57.2091 55 59 53.2091 59 51V1ZM5 0C2.23858 0 0 2.23858 0 5V56H55C57.7614 56 60 53.7614 60 51V0H5Z"
                              fill="#397345"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <a href="{{ route('products.detail', ['slug' => $product->slug]) }}" class="card__link"></a>
</article>
