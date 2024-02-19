@extends('layouts.backend')

@section('pagetitle', 'Добавить свойство')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.properties.index') }}" class="back-link">
        Свойства
    </a>

    <div class="pagetitle">
        Добавить свойство
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

    <form action="{{ route('backend.properties.store') }}" method="post" enctype="multipart/form-data">
        @include('backend.shop.property.form')
    </form>

@endsection



@section('scripts')
@endsection
