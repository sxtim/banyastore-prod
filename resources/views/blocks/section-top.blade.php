<section class="top section">
    <div class="container">
        <div class="top__slider">
            <div id="keen-slider-top" class="keen-slider">
                @foreach($banners as $banner)
                    <a class="keen-slider__slide" href="{{ $banner->link }}">
                        <img
                            src="{{ $banner->getUrlImage() }}"
                            alt="{{ $banner->name }}"
                            width="1296"
                            height="697"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                            decoding="{{ $loop->first ? 'sync' : 'async' }}"
                        >
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
