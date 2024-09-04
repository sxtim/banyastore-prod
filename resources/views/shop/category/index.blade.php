@extends('layouts.app')

@section('pagetitle', 'Категории | Banyastore')

@section('content')
    {{ Breadcrumbs::render('category-list') }}
    <section class="catalog section">
        <div class="container">
            <h1 class="catalog__title title-s">
                Категории
            </h1>
            <div class="catalog__wrapper">
                <div class="catalog__cards-wrapper">
                    @foreach($categories as $category)

                        <article class="card-catalog">

                            @if ($category->getImageUrlAttribute())
                                <div class="card-catalog__picture">
                                    <img src="{{ $category->getImageUrlAttribute() }}"/>
                                </div>
                            @endif
                            <div class="card-catalog__title btn btn-medium">
                                {{ $category->name }}
                            </div>
                            <a class="card-news__link"
                               href="{{ route('products.by-category', ['slug' => $category->slug]) }}"> </a>
                        </article>

                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
