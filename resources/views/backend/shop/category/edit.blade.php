@extends('layouts.backend')

@section('pagetitle', 'Редактировать категорию')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.categories.index') }}" class="back-link">
        Список категорий
    </a>

    <div class="pagetitle">
        Редактировать категорию
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

    <form action="{{ route('backend.categories.update', ['category' => $category->id]) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @include('backend.shop.category.form')
    </form>


@endsection

@section('scripts')
@endsection
