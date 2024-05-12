@extends('layouts.app')

@section('pagetitle', 'Избранные продукты | Banyastore')

@section('content')
    {{ Breadcrumbs::render('favorites') }}
    <section class="catalog section" id="catalog">
        <div class="container">
            <h1 class="catalog__title title-s">
                Избранное
            </h1>
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
