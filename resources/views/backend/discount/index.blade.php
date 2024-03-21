@extends('layouts.backend')

@section('pagetitle', 'Скидки')
@section('aside_discounts', 'active')

@section('content')

    <a href="{{ route('backend.shop.index') }}" class="back-link">
        Интернет-магазин
    </a>

    <div class="pagetitle">
        Скидки
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
            Найдено скидок: {{ $discounts->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.discount.create') }}" class="btn btn-outline-primary">
                Добавить скидку
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $discounts->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Название</th>
                    <th>Активность</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($discounts as $discount)
                    <tr>
                        <td>
                            {{ $discount->id }}
                        </td>
                        <td>
                            {{ $discount->name }}
                        </td>
                        <td>
                            {{ $discount->is_active }}
                        </td>
                        <td>
                            <a href="{{ route('backend.discount.edit', ['discountId' => $discount->id]) }}">
                                Ред.
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Скидки не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $discounts->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
