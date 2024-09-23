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
            <div class="catalog__filter-container">
                        <details class="catalog__filter-details">
                            <summary>Your favourite cars list</summary>
                            <form>
                                <fieldset>
                                    <legend>Cars</legend>
                                    <ul>
                                        <li>
                                            <label for="bmw">BMW<input type="checkbox" id="bmw" name="bmw"
                                                                       value="bmw"></label>
                                        </li>
                                        <li>
                                            <label for="citroen">Citroen
                                                <input type="checkbox" id="citroen" name="citroen"
                                                       value="citroen"></label>
                                        </li>
                                        <li>
                                            <label for="skoda">Skoda
                                                <input type="checkbox" id="skoda" name="skoda" value="skoda"></label>
                                        </li>
                                        <li>
                                            <label for="volvo">Volvo
                                                <input type="checkbox" id="volvo" name="volvo" value="volvo"></label>
                                        </li>
                                    </ul>
                                </fieldset>
                            </form>
                        </details>
                        <details class="catalog__filter-details">
                            <summary>Your favourite cars list</summary>
                            <form>
                                <fieldset>
                                    <legend>Cars</legend>
                                    <ul>
                                        <li>
                                            <label for="bmw">BMW<input type="checkbox" id="bmw" name="bmw"
                                                                       value="bmw"></label>
                                        </li>
                                        <li>
                                            <label for="citroen">Citroen
                                                <input type="checkbox" id="citroen" name="citroen"
                                                       value="citroen"></label>
                                        </li>
                                        <li>
                                            <label for="skoda">Skoda
                                                <input type="checkbox" id="skoda" name="skoda" value="skoda"></label>
                                        </li>
                                        <li>
                                            <label for="volvo">Volvo
                                                <input type="checkbox" id="volvo" name="volvo" value="volvo"></label>
                                        </li>
                                    </ul>
                                </fieldset>
                            </form>
                        </details>
                    <details class="catalog__filter-details">
                        <summary>Your favourite cars list</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Cars</legend>
                                <ul>
                                    <li>
                                        <label for="bmw">BMW<input type="checkbox" id="bmw" name="bmw"
                                                                   value="bmw"></label>
                                    </li>
                                    <li>
                                        <label for="citroen">Citroen
                                            <input type="checkbox" id="citroen" name="citroen" value="citroen"></label>
                                    </li>
                                    <li>
                                        <label for="skoda">Skoda
                                            <input type="checkbox" id="skoda" name="skoda" value="skoda"></label>
                                    </li>
                                    <li>
                                        <label for="volvo">Volvo
                                            <input type="checkbox" id="volvo" name="volvo" value="volvo"></label>
                                    </li>
                                    <li>
                                        <label for="volvo">Volvo
                                            <input type="checkbox" id="volvo" name="volvo" value="volvo"></label>
                                    </li>
                                    <li>
                                        <label for="volvo">Volvo
                                            <input type="checkbox" id="volvo" name="volvo" value="volvo"></label>
                                    </li>
                                    <li>
                                        <label for="volvo">Volvo
                                            <input type="checkbox" id="volvo" name="volvo" value="volvo"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
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
