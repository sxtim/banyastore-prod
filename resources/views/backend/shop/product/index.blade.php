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
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
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
        $(document).ready(function() {
            $("#searchForm").submit(function() {
                $("#search").val($("#search").val().trim())
                if($("#search").val() == "") {
                    $("#search").attr('disabled', true);
                }
                if($("#category").val() == "") {
                    $("#category").attr('disabled', true);
                }
                if($("#purveyor").val() == "") {
                    $("#purveyor").attr('disabled', true);
                }
            });
        });
    </script>
@endsection
