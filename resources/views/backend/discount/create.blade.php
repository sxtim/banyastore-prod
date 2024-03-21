@extends('layouts.backend')

@section('pagetitle', 'Добавить скидку')
@section('aside_discounts', 'active')

@section('content')

    <a href="{{ route('backend.discount.index') }}" class="back-link">
        Скидки
    </a>

    <div class="pagetitle">
        Добавить скидку
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

    <form action="{{ route('backend.discount.store') }}" method="post" enctype="multipart/form-data">
        @include('backend.discount.form')
    </form>

@endsection


@section('scripts')

@endsection
