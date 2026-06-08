<section class="top section">
    <div class="container">
        <div class="top__slider">
            <div id="keen-slider-top" class="keen-slider">
                @foreach($banners as $banner)
                    <a class="keen-slider__slide" href="{{ $banner->link }}">
                        <picture>
                            @if($banner->getUrlImageMobileWebp())
                                <source media="(max-width: 767px)" srcset="{{ $banner->getUrlImageMobileWebp() }}" type="image/webp">
                            @endif
                            @if($banner->getUrlImageDesktopWebp())
                                <source srcset="{{ $banner->getUrlImageDesktopWebp() }}" type="image/webp">
                            @endif
                            <img
                                src="{{ $banner->getUrlImage() }}"
                                alt="{{ $banner->name }}"
                                width="1296"
                                height="697"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                                decoding="{{ $loop->first ? 'sync' : 'async' }}"
                            >
                        </picture>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
