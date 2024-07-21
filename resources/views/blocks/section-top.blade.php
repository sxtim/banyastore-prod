<section class="top section">
    <div class="container">
        <div class="top__slider">
            <div id="keen-slider-top" class="keen-slider">
                @foreach($banners as $banner)
                    <div class="keen-slider__slide">
                        <img src="{{ $banner->getUrlImage() }}" alt="{{ $banner->name }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
