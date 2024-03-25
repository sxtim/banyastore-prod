@extends('layouts.backend')

@section('pagetitle', 'Продукты')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.shop.index') }}" class="back-link">
        Интернет-магазин
    </a>

    <div class="pagetitle">
        Продукты
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('backend.product.index') }}" method="get" id="searchForm" class="form-wrap-modif">
                <div class="filter-box">
                    <div class="filter-search filter-row">
                        <input type="search" name="search-product" id="search-product" placeholder="Поиск по названию" class="form-control"
                               value="{{ $filters['search-product'] ?? '' }}">
                    </div>
                    <div class="row filter-row">
                        <div class="col">
                            <label for="category" class="main-label">Категория</label>
                            <select name="category" id="category" class="form-control">
                                <option value="" selected>Не выбрана</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ isset($filters['category']) && $filters['category'] == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg btn-wrap">
                        <a href="{{ route('backend.product.index') }}" class="btn btn-secondary" style="align-self: flex-end">Сбросить фильтр</a>
                        <button type="submit" class="btn btn-primary" style="align-self: flex-end">Применить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="top-row">
        <div class="top-row-text">
            Найдено продуктов: {{ $products->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.product.create') }}" class="btn btn-outline-primary">Добавить продукт</a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $products->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Сортировка</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->sort }}</td>
                        <td><a href="{{ route('backend.product.edit', ['product' => $product->id]) }}">Ред.</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Продукты не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $products->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            if (document.getElementById('search-product').value === '') {
                document.getElementById('search-product').setAttribute('disabled','disabled')
            }
            if (document.getElementById('category').value === '') {
                document.getElementById('category').setAttribute('disabled','disabled')
            }
        })
    </script>
@endsection
