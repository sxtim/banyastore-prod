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
                <div class="filter-item">
                    <details class="catalog__filter-details">
                        <summary>Бренд</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Бренд</legend>
                                <ul>
                                    <li>
                                        <label for="1">1<input type="checkbox" id="1" name="1"
                                                                   value="1"></label>
                                    </li>
                                    <li>
                                        <label for="2">2
                                            <input type="checkbox" id="2" name="2"
                                                   value="2"></label>
                                    </li>
                                    <li>
                                        <label for="3">3
                                            <input type="checkbox" id="3" name="3" value="3"></label>
                                    </li>
                                    <li>
                                        <label for="4">4
                                            <input type="checkbox" id="4" name="4" value="4"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
                </div>
                <div class="filter-item">
                    <details class="catalog__filter-details">
                        <summary>Объем парной</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Объем парной</legend>
                                <ul>
                                    <li>
                                        <label for="11">11<input type="checkbox" id="11" name="11"
                                                                   value="11"></label>
                                    </li>
                                    <li>
                                        <label for="22">Citroen
                                            <input type="checkbox" id="22" name="22"
                                                   value="citroen"></label>
                                    </li>
                                    <li>
                                        <label for="33">Skoda
                                            <input type="checkbox" id="33" name="33" value="33"></label>
                                    </li>
                                    <li>
                                        <label for="44">Volvo
                                            <input type="checkbox" id="44" name="44" value="44"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
                </div>
                <div class="filter-item">
                    <details class="catalog__filter-details">
                        <summary>Тип печи</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Cars</legend>
                                <ul>
                                    <li>
                                        <label for="111">111<input type="checkbox" id="111" name="111"
                                                                   value="111"></label>
                                    </li>
                                    <li>
                                        <label for="222">222
                                            <input type="checkbox" id="222" name="222" value="222"></label>
                                    </li>
                                    <li>
                                        <label for="333">333
                                            <input type="checkbox" id="333" name="333" value="333"></label>
                                    </li>
                                    <li>
                                        <label for="444">444
                                            <input type="checkbox" id="444" name="444" value="444"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
                </div>
                <div class="filter-item">
                    <details class="catalog__filter-details">
                        <summary>Материал топки</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Материал топки</legend>
                                <ul>
                                    <li>
                                        <label for="111">BMW<input type="checkbox" id="111" name="111"
                                                                   value="111"></label>
                                    </li>
                                    <li>
                                        <label for="222">Citroen
                                            <input type="checkbox" id="222" name="222" value="222"></label>
                                    </li>
                                    <li>
                                        <label for="333">Skoda
                                            <input type="checkbox" id="333" name="333" value="333"></label>
                                    </li>
                                    <li>
                                        <label for="444">Volvo
                                            <input type="checkbox" id="444" name="444" value="444"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
                </div>
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
