<section class="news-main section">
    <div class="container">
        <div class="news-main__title title-s">Новостной блог</div>
        <div class="news-main__wrapper">
            @foreach($news as $itemNews)
                @include('blocks/card-news')
            @endforeach
            <article class="card-news card-news-main">
                <div class="card-news-main__desc">
                    <h2 class="card-news-main__title">Новостной блог</h2>
                    <h3 class="card-news-main__sub-title">Раздел с самыми актуальными новостями в банной сфере. Про новинки в печной индустрии. Все самое полезное и интересное.</h3>
                    <a href="{{ route('news.index') }}" class="card-news-main__link btn btn-medium">ВСЕ  НОВОСТИ</a>
                </div>
            </article>
        </div>
    </div>
</section>
