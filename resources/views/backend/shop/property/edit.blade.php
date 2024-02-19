@extends('layouts.backend')

@section('pagetitle', 'Редактировать свойство')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.properties.index') }}" class="back-link">
        Свойства
    </a>

    <div class="pagetitle">
        Редактировать свойство
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

    <form class="form-wrap-modif" action="{{ route('backend.properties.update', ['property' => $property->id]) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @include('backend.shop.property.form')
    </form>

@endsection

@section('scripts')
@endsection
