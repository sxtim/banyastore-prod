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
                                                           name="properties[]"
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
