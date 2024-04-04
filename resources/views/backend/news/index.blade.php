@extends('layouts.backend')

@section('pagetitle', 'Новости')
@section('aside_news', 'active')

@section('content')

    <a href="{{ route('backend.index') }}" class="back-link">
        Панель управления
    </a>

    <div class="pagetitle">
        Новости
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
            Найдено новостей: {{ $news->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.news.create') }}" class="btn btn-outline-primary">
                Добавить новость
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $news->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Название</th>
                    <th>Сортировка</th>
                    <th>Активность</th>
                    <th>Дата начала</th>
                    <th>Дата конца</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($news as $item)
                    <tr>
                        <td>
                            {{ $item->id }}
                        </td>
                        <td>
                            {{ $item->name }}
                        </td>
                        <td>
                            {{ $item->sort }}
                        </td>
                        <td>
                            {{ $item->is_active ? 'Да' : 'Нет' }}
                        </td>
                        <td>
                            {{ $item->start_at }}
                        </td>
                        <td>
                            {{ $item->end_at }}
                        </td>
                        <td>
                            <a href="{{ route('backend.news.edit', ['newsId' => $item->id]) }}">
                                Ред.
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Новости не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $news->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
