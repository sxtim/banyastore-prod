@extends('layouts.backend')

@section('pagetitle', 'Интернет-магазин - Категории')
@section('aside_shop', 'active')

@section('content')

@if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif
<a href="{{ route('backend.shop.index') }}" class="back-link">
    Интернет-магазин
</a>

    <div class="shop">
        <div class="shop-content">
            <div class="row">
                <div class="col"><h1>Категории</h1></div>
            </div>
            <div class="col-3" style="margin-bottom: 20px">
                <a href="{{ route('backend.categories.create') }}" class="btn btn-block btn-outline-primary">
                    Добавить новую
                </a>
            </div>

            <div class="list-group">
                @foreach($categories as $taxonomy)
                <ul class="list-group-item">
                    <li style="margin: 10px 0">
                        <a href="{{ route('backend.categories.edit',  ['category' => $taxonomy->id]) }}">
                            {{$taxonomy->name}}
                        </a>
                    </li>
                @if(count($taxonomy->subcategory))
                    @include('backend.shop.category.sub_category_list',['subcategories' => $taxonomy->subcategory])
                @endif
                </ul>
            @endforeach
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection
