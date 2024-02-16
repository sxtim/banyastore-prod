@extends('layouts.backend')

@section('pagetitle', 'Интернет-магазин')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.index') }}" class="back-link">
        Панель управления
    </a>

    <div class="pagetitle">
        Интернет магазин
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('backend.product.index') }}" class="btn btn-block btn-outline-primary">
                Продукты
            </a>
        </div>
    </div>


@endsection

@section('scripts')

@endsection
