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
                                        <label for="22">22
                                            <input type="checkbox" id="22" name="22"
                                                   value="citroen"></label>
                                    </li>
                                    <li>
                                        <label for="33">33
                                            <input type="checkbox" id="33" name="33" value="33"></label>
                                    </li>
                                    <li>
                                        <label for="44">44
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
                        <summary>Материал</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Материал</legend>
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
                        <summary>Тип каменки</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Тип каменки</legend>
                                <ul>
                                    <li>
                                        <label for="1111">1111<input type="checkbox" id="1111" name="111"
                                                                     value="111"></label>
                                    </li>
                                    <li>
                                        <label for="2222">2222
                                            <input type="checkbox" id="2222" name="2222" value="2222"></label>
                                    </li>
                                    <li>
                                        <label for="3333">3333
                                            <input type="checkbox" id="3333" name="3333" value="3333"></label>
                                    </li>
                                    <li>
                                        <label for="4444">4444
                                            <input type="checkbox" id="4444" name="4444" value="4444"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
                </div>
                <div class="filter-item">
                    <details class="catalog__filter-details">
                        <summary>Загрузка каменки</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Загрузка каменки</legend>
                                <ul>
                                    <li>
                                        <label for="11111">11111<input type="checkbox" id="11111" name="11111"
                                                                       value="11111"></label>
                                    </li>
                                    <li>
                                        <label for="22222">22222
                                            <input type="checkbox" id="22222" name="22222" value="22222"></label>
                                    </li>
                                    <li>
                                        <label for="33333">33333
                                            <input type="checkbox" id="33333" name="33333" value="33333"></label>
                                    </li>
                                    <li>
                                        <label for="444">444
                                            <input type="checkbox" id="44444" name="44444" value="44444"></label>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </details>
                </div>
                <div class="filter-item">
                    <details class="catalog__filter-details">
                        <summary>Вид топлива</summary>
                        <form>
                            <fieldset class="filter-el">
                                <legend>Вид топлива</legend>
                                <ul>
                                    <li>
                                        <label for="111111">111111<input class="real-checkbox" type="checkbox"
                                                                         id="111111" name="111111"
                                                                         value="111111">
                                            <span class="custom-checkbox"></span></label>
                                    </li>
                                    <li>
                                        <label for="222222">222222
                                            <input class="real-checkbox" type="checkbox" id="222222" name="222222"
                                                   value="222222">
                                            <span class="custom-checkbox"></span></label>
                                    </li>
                                    <li>
                                        <label for="333333">333333
                                            <input class="real-checkbox" type="checkbox" id="333333" name="333333"
                                                   value="333333">
                                            <span class="custom-checkbox"></span></label>
                                    </li>
                                    <li>
                                        <label for="444444">444444
                                            <input class="real-checkbox" type="checkbox" id="444444" name="444444"
                                                   value="444444">
                                            <span class="custom-checkbox"></span></label>
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
