<section class="news-main section">
  <div class="container">
    <div class="news-main__title title-s">Новостной блог</div>
    <div class="news-main__wrapper">
      @@loop('card-news.html','./data/card-news.json')
      <article class="card-news card-news-main">
        <div class="card-news-main__desc">
          <h2 class="card-news-main__title">Новостной блог</h2>
          <h3 class="card-news-main__sub-title">Раздел с самыми актуальными новостями в банной сфере. Про новинки в печной индустрии. Все самое полезное и интересное.</h3>
          <a href="./news.html" class="card-news-main__link btn btn-medium">ВСЕ  НОВОСТИ</a>
        </div>
      </article>
      @@loop('card-news.html','./data/card-news.json')
      @@loop('card-news.html','./data/card-news.json')
    </div>
  </div>
</section>