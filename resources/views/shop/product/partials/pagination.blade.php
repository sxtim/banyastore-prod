@if ($paginator->hasPages())
    <nav class="catalog-pagination" aria-label="Навигация по страницам каталога" data-catalog-pagination>
        <ul class="catalog-pagination__list">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="catalog-pagination__link catalog-pagination__link--disabled" aria-disabled="true" aria-label="Предыдущая страница">
                        <span aria-hidden="true">←</span>
                    </span>
                @else
                    <a class="catalog-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Предыдущая страница">
                        <span aria-hidden="true">←</span>
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="catalog-pagination__ellipsis" aria-hidden="true">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page === $paginator->currentPage())
                                <span class="catalog-pagination__link catalog-pagination__link--active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a class="catalog-pagination__link" href="{{ $url }}" aria-label="Страница {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a class="catalog-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Следующая страница">
                        <span aria-hidden="true">→</span>
                    </a>
                @else
                    <span class="catalog-pagination__link catalog-pagination__link--disabled" aria-disabled="true" aria-label="Следующая страница">
                        <span aria-hidden="true">→</span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
