@extends('layouts.backend')

@section('pagetitle', 'Seo')
@section('aside_seo', 'active')

@section('content')

    <a href="{{ route('backend.index') }}" class="back-link">
        Панель управления
    </a>

    <div class="pagetitle">
        Seo
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
            Найдено шаблонов: {{ $seoTemplates->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.seo.create') }}" class="btn btn-outline-primary">
                Добавить шаблон
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $seoTemplates->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>По умолчанию</th>
                    <th>Тип материала</th>
                    <th>Категория</th>
                    <th>Тип шаблона</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($seoTemplates as $item)
                    <tr>
                        <td>
                            {{ $item->id }}
                        </td>
                        <td>
                            {{ $item->is_main ? 'Да' : 'Нет' }}
                        </td>
                        <td>
                            {{ $item->getTypeMaterial() }}
                        </td>
                        <td>
                            {{ $item->category ? $item->category->name : '' }}
                        </td>
                        <td>
                            {{ $item->type_template }}
                        </td>
                        <td>
                            <a href="{{ route('backend.seo.edit', ['templateId' => $item->id]) }}">
                                Редактировать
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            Шаблоны не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $seoTemplates->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
