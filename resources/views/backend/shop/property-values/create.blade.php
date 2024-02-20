@extends('layouts.backend')

@section('pagetitle', 'Добавить значение свойства')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.property-values.index', ['propertyId' => $property->id]) }}" class="back-link">
        Значение свойств
    </a>

    <div class="pagetitle">
        Добавить значение свойства {{ $property->name }}
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

    <form action="{{ route('backend.property-values.store', ['propertyId' => $property->id]) }}" method="post" enctype="multipart/form-data">
        @include('backend.shop.property-values.form')
    </form>

@endsection



@section('scripts')
@endsection
