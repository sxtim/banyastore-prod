<section class="catalog section">
    <div class="container">
        <h1 class="catalog__title title-s">Новостной блог</h1>
        <div class="catalog__wrapper">
            <div class="catalog-news__cards-wrapper">
                @foreach($news as $itemNews)
                    @include('blocks/card-news')
                @endforeach
            </div>
            <div class="catalog-pagination__container">
                <!--      <button class="catalog__rectangle-limiter rectangle-limiter">-->
                <!--        <div class="catalog__circular-arrow circular-arrow"></div>-->
                <!--        <span>Показать ещё</span>-->
                <!--      </button>-->
                {{--        @@include('pagination-page.html')--}}
            </div>
        </div>
    </div>
</section>
