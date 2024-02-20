@extends('layouts.backend')

@section('pagetitle', 'Редактировать значение свойства')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.property-values.index', ['propertyId' => $propertyValue->property->id]) }}" class="back-link">
        Значение свойств
    </a>

    <div class="pagetitle">
        Редактировать значение свойства {{ $propertyValue->property->name }}
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

    <form class="form-wrap-modif" action="{{ route('backend.property-values.update', ['propertyValueId' => $propertyValue->id]) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @include('backend.shop.property-values.form')
    </form>

@endsection

@section('scripts')
@endsection
