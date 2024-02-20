@extends('layouts.backend')

@section('pagetitle', 'Значения свойства')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.properties.index') }}" class="back-link">
        Свойства
    </a>

    <div class="pagetitle">
        Значения свойства {{ $property->name }}
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
            Найдено значений: {{ $values->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.property-values.create', ['propertyId' => $property->id]) }}" class="btn btn-outline-primary">
                Добавить значение
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $values->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>Значение</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($values as $value)
                    <tr>
                        <td>{{ $value->name }}</td>
                        <td>
                            <a href="{{ route('backend.property-values.edit', ['propertyValueId' => $value->id]) }}">
                                Ред.
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Значения не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $values->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
