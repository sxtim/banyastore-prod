@extends('layouts.backend')

@section('pagetitle', 'Баннеры')
@section('aside_banner', 'active')

@section('content')

    <a href="{{ route('backend.index') }}" class="back-link">
        Панель управления
    </a>

    <div class="pagetitle">
        Баннеры
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
            Найдено баннеров: {{ $banners->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.banner.create') }}" class="btn btn-outline-primary">
                Добавить баннер
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $banners->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Название</th>
                    <th>Сортировка</th>
                    <th>Активность</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($banners as $banner)
                    <tr>
                        <td>
                            {{ $banner->id }}
                        </td>
                        <td>
                            {{ $banner->name }}
                        </td>
                        <td>
                            {{ $banner->sort }}
                        </td>
                        <td>
                            {{ $banner->is_active }}
                        </td>
                        <td>
                            <a href="{{ route('backend.banner.edit', ['bannerId' => $banner->id]) }}">
                                Ред.
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Баннеры не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $banners->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
