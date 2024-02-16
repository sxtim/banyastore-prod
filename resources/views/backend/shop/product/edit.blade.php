@extends('layouts.backend')

@section('pagetitle', 'Редактировать продукт')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.product.index') }}" class="back-link">
        Продукты
    </a>

    <div class="pagetitle">
        Редактировать продукт
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="form-wrap-modif" action="{{ route('backend.product.update', ['product' => $product->id]) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @include('backend.shop.product.form')
    </form>

@endsection

@section('scripts')
@endsection
