<article class="card-news">
    <div class="card-news__picture">
        <img src="{{ $itemNews->getPreviewImg() }}" alt="{{ $itemNews->name }}">
    </div>
    <div class="card-news__desc">
        <h3 class="card-news__title">
            {{ $itemNews->name }}
        </h3>
        <h4 class="card-news__sub-title">
            {{ $itemNews->preview_text }}
        </h4>
        <time class="card-news__date">
            {{ $itemNews->start_at->format('d.m.Y') }}
        </time>
    </div>
    <a href="#!" class="card-news__link"></a>
</article>
