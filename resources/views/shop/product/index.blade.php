@extends('layouts.app')

@section('pagetitle', $seo->getTitle())
@section('description', $seo->getDescription())

@section('content')
    {{ Breadcrumbs::render('category', $category) }}
    <section class="catalog section" id="catalog">
        <div class="container">
            <h1 class="catalog__title title-s">
                {{ $category->name }}
            </h1>
            <form action="{{ route('products.by-category', ['slug' => $category->slug]) }}">
                <div class="catalog__filter-container">
                    @foreach($properties as $property)
                        <div class="filter-item">
                            <details class="catalog__filter-details">
                                <summary>{{ $property['name'] }}</summary>
                                <fieldset class="filter-el">
                                    <legend>{{ $property['name'] }}</legend>
                                    <ul>
                                        @foreach($property['values'] as $value)
                                            <li>
                                                <label for="{{ $value['id'] }}">
                                                    {{ $value['name'] }}
                                                    <input class="real-checkbox"
                                                           type="checkbox"
                                                           id="{{ $value['id'] }}"
                                                           name="properties[{{ $property['id'] }}][]"
                                                           value="{{ $value['id'] }}"
                                                           {{ isset($filters) && isset($filters['properties']) && in_array($value['id'], $filters['properties']) ? 'checked' : '' }}
                                                    >
                                                    <span class="custom-checkbox"></span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </fieldset>
                            </details>
                        </div>
                    @endforeach
                    <button class="catalog__filer-btn btn btn-medium">ПОИСК</button>
                </div>
            </form>
            @php
                $queryParams = request()->except(['sort', 'sortDesk']);
            @endphp
            <div class="sort-block">
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
            <div class="catalog__wrapper">
                <div class="catalog__cards-wrapper">
                    @foreach($products as $product)
                        @include('blocks/card-product')
                    @endforeach
                </div>
                <div class="catalog-pagination__container">
                    {{--                    @@include('blocks/pagination-page.html')--}}
                </div>
            </div>
        </div>
    </section>
@endsection
