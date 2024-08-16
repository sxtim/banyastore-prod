@extends('layouts.app')

@section('pagetitle', 'Категории | Banyastore')

@section('content')
    {{ Breadcrumbs::render('category-list') }}
    <section class="catalog section">
        <div class="container">
            <h1 class="catalog__title title-s">
                Категории
            </h1>
            @foreach($categories as $category)
                <div class="catalog__wrapper">
                    <a href="{{ route('products.by-category', ['slug' => $category->slug]) }}" >
                        @if ($category->getImageUrlAttribute())
                            <div>
                                <img src="{{ $category->getImageUrlAttribute() }}" />
                            </div>
                        @endif
                        <div>
                            {{ $category->name }}
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endsection
