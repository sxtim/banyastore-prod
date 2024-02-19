@extends('layouts.backend')

@section('pagetitle', 'Свойства')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.shop.index') }}" class="back-link">
        Интернет-магазин
    </a>

    <div class="pagetitle">
        Свойства
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">

        </div>
    </div>

    <div class="top-row">
        <div class="top-row-text">
            Найдено свойств: {{ $properties->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.properties.create') }}" class="btn btn-outline-primary">
                Добавить свойство
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $properties->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Обязательное?</th>
                    <th>Значения</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($properties as $property)
                    <tr>
                        <td>{{ $property->name }}</td>
                        <td>
                            {{ $property->is_required ? 'Да' : 'Нет' }}
                        </td>
                        <td>

                        </td>
                        <td>
                            <a href="{{ route('backend.properties.edit', ['property' => $property->id]) }}">
                                Ред.
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Свойства не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $properties->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
