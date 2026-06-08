<article class="card-news">
    <div class="card-news__picture">
        <img src="{{ $itemNews->getPreviewImg() }}" alt="{{ $itemNews->name }}" loading="lazy" decoding="async">
    </div>
    <div class="card-news__desc">
        <div class="card-news__title">
            {{ $itemNews->name }}
        </div>
        <div class="card-news__sub-title">
            {{ $itemNews->preview_text }}
        </div>
        <time class="card-news__date">
            {{ $itemNews->start_at->format('d.m.Y') }}
        </time>
    </div>
    <a href="{{ route('news.detail', ['slug' => $itemNews->slug]) }}" class="card-news__link"></a>
</article>
