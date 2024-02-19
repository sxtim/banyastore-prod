@extends('layouts.backend')

@section('pagetitle', 'Добавить категорию')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.categories.index') }}" class="back-link">
        Категории
    </a>

    <div class="pagetitle">
        Добавить категорию
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

    <form action="{{ route('backend.categories.store') }}" method="post" enctype="multipart/form-data">
        @include('backend.shop.category.form')
    </form>

@endsection

@section('scripts')
@endsection
