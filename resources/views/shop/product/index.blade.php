@extends('layouts.app')

@section('pagetitle', $seo->getTitle().($products->currentPage() > 1 ? ' — страница '.$products->currentPage() : ''))
@section('description', $seo->getDescription())
@section('canonical', request()->except('page') === [] && $products->currentPage() > 1 ? $products->url($products->currentPage()) : route('products.by-category', ['slug' => $category->slug]))

@if(request()->except('page') !== [])
    @section('head')
        <meta name="robots" content="noindex,follow">
    @endsection
@endif

@section('content')
    {{ Breadcrumbs::render('category', $category) }}
    <section class="catalog section">
        <div class="container">
            <h1 class="catalog__title title-s">
                {{ $category->name }}
            </h1>
            <div class="catalog__layout">
                <aside class="catalog__sidebar" aria-label="Фильтры каталога">
                    <form id="catalog-filter-form" action="{{ route('products.by-category', ['slug' => $category->slug]) }}" method="get">
                        @if(isset($filters['sort']))
                            <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                        @elseif(isset($filters['sortDesk']))
                            <input type="hidden" name="sortDesk" value="{{ $filters['sortDesk'] }}">
                        @endif
                        <div id="catalog-filter-app">
                            <catalog-filters-component
                                :properties="{{ Js::from($properties) }}"
                                :initial-selected="{{ Js::from((object) ($filters['properties'] ?? [])) }}"
                                :initial-facets="{{ Js::from((object) $facets) }}"
                                :initial-count="{{ $resultCount }}"
                                :initial-selected-price="{{ Js::from((object) $selectedPrice) }}"
                                :initial-price-bounds="{{ Js::from((object) $priceBounds) }}"
                                :featured-property-ids="{{ Js::from($filterUi['featured_property_ids']) }}"
                                :brand-property-id="{{ Js::from($filterUi['brand_property_id']) }}"
                                :always-open-property-ids="{{ Js::from($filterUi['always_open_property_ids']) }}"
                                :option-preview-limit="{{ $filterUi['option_preview_limit'] }}"
                                facets-url="{{ route('ajax.category.facets', ['slug' => $category->slug]) }}"
                                results-url="{{ route('products.by-category', ['slug' => $category->slug]) }}"
                            >
                                @include('shop.product.partials.filters-fallback')
                            </catalog-filters-component>
                        </div>
                    </form>
                </aside>

                <div class="catalog__content">
                    @php
                        $queryParams = request()->except(['sort', 'sortDesk', 'page']);
                    @endphp
                    <div class="sort-block" data-catalog-sort>
                        <span class="sort-block__label">Сортировать:</span>
                        <a href="{{ url()->current() }}?{{ http_build_query(array_merge($queryParams, ['sort' => 'price'])) }}">
                            Дешевле
                        </a>
                        <a href="{{ url()->current() }}?{{ http_build_query(array_merge($queryParams, ['sortDesk' => 'price'])) }}">
                            Дороже
                        </a>
                        <a href="{{ url()->current() }}?{{ http_build_query(array_merge($queryParams, ['sort' => 'action'])) }}">
                            Акция
                        </a>
                        <a href="{{ url()->current() }}?{{ http_build_query(array_merge($queryParams, ['sort' => 'new'])) }}">
                            Новинка
                        </a>
                        <a href="{{ url()->current() }}?{{ http_build_query(array_merge($queryParams, ['sort' => 'hit'])) }}">
                            Хит
                        </a>
                        <a href="{{ url()->current() }}?{{ http_build_query(array_merge($queryParams, ['sort' => 'popular'])) }}">
                            Популярное
                        </a>
                    </div>
                    <div class="catalog__wrapper" id="catalog-products-app" aria-live="polite">
                        @include('shop.product.partials.results')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
