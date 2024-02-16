@extends('layouts.backend')

@section('pagetitle', 'Добавить продукт')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.product.index') }}" class="back-link">
        Продукты
    </a>

    <div class="pagetitle">
        Добавить продукт
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

    <form action="{{ route('backend.product.store') }}" method="post" enctype="multipart/form-data">
        @include('backend.shop.product.form')
    </form>

@endsection



@section('scripts')
@endsection
