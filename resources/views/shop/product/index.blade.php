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
                                            <label for="1">1<input class="real-checkbox" type="checkbox" id="1" name="1"
                                                                   value="1"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="2">2
                                                <input class="real-checkbox" type="checkbox" id="2" name="2"
                                                       value="2"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="3">3
                                                <input class="real-checkbox" type="checkbox" id="3" name="3" value="3"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="4">4
                                                <input class="real-checkbox" type="checkbox" id="4" name="4" value="4"><span class="custom-checkbox"></span></label>
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
                                            <label for="11">11<input class="real-checkbox" type="checkbox" id="11" name="11"
                                                                     value="11"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="22">22
                                                <input class="real-checkbox" type="checkbox" id="22" name="22"
                                                       value="citroen"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="33">33
                                                <input class="real-checkbox" type="checkbox" id="33" name="33" value="33"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="44">44
                                                <input class="real-checkbox" type="checkbox" id="44" name="44" value="44"><span class="custom-checkbox"></span></label>
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
                                            <label for="2111">2111<input class="real-checkbox" type="checkbox" id="2111" name="2111"
                                                                       value="2111"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="1222">1222
                                                <input class="real-checkbox" type="checkbox" id="1222" name="1222" value="1222"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="2333">2333
                                                <input class="real-checkbox" type="checkbox" id="2333" name="2333" value="2333"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="2444">2444
                                                <input class="real-checkbox" type="checkbox" id="2444" name="2444" value="2444"><span class="custom-checkbox"></span></label>
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
                                            <label for="111">111<input class="real-checkbox" type="checkbox" id="111" name="111"
                                                                       value="111"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="222">222
                                                <input class="real-checkbox" type="checkbox" id="222" name="222" value="222"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="333">333
                                                <input class="real-checkbox" type="checkbox" id="333" name="333" value="333"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="444">444
                                                <input class="real-checkbox" type="checkbox" id="444" name="444" value="444"><span class="custom-checkbox"></span></label>
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
                                            <label for="1111">1111<input class="real-checkbox" type="checkbox" id="1111" name="111"
                                                                         value="111"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="2222">2222
                                                <input class="real-checkbox" type="checkbox" id="2222" name="2222" value="2222"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="3333">3333
                                                <input class="real-checkbox" type="checkbox" id="3333" name="3333" value="3333"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="4444">4444
                                                <input class="real-checkbox" type="checkbox" id="4444" name="4444" value="4444"><span class="custom-checkbox"></span></label>
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
                                            <label for="11111">11111<input class="real-checkbox" type="checkbox" id="11111" name="11111"
                                                                           value="11111"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="22222">22222
                                                <input class="real-checkbox" type="checkbox" id="22222" name="22222" value="22222"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="33333">33333
                                                <input class="real-checkbox" type="checkbox" id="33333" name="33333" value="33333"><span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="44444">44444
                                                <input class="real-checkbox" type="checkbox" id="44444" name="44444" value="44444"><span class="custom-checkbox"></span></label>
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
                    <div class="filter-item">
                        <details class="catalog__filter-details">
                            <summary>Сортировка</summary>
                            <form>
                                <fieldset class="filter-el">
                                    <legend>Сортировка</legend>
                                    <ul>
                                        <li>
                                            <label for="1111111">1111111<input class="real-checkbox" type="checkbox"
                                                                             id="1111111" name="1111111"
                                                                             value="1111111">
                                                <span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="2222222">2222222
                                                <input class="real-checkbox" type="checkbox" id="2222222" name="2222222"
                                                       value="2222222">
                                                <span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="3333333">3333333
                                                <input class="real-checkbox" type="checkbox" id="3333333" name="3333333"
                                                       value="3333333">
                                                <span class="custom-checkbox"></span></label>
                                        </li>
                                        <li>
                                            <label for="4444444">4444444
                                                <input class="real-checkbox" type="checkbox" id="4444444" name="4444444"
                                                       value="4444444">
                                                <span class="custom-checkbox"></span></label>
                                        </li>
                                    </ul>
                                </fieldset>
                            </form>
                        </details>
                    </div>
                    <button class="catalog__filer-btn btn btn-medium">ПОИСК</button>
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
