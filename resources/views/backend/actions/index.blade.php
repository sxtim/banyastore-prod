@extends('layouts.backend')

@section('pagetitle', 'Акции')
@section('aside_actions', 'active')

@section('content')

    <a href="{{ route('backend.index') }}" class="back-link">
        Панель управления
    </a>

    <div class="pagetitle">
        Акции
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
            Найдено акций: {{ $actions->total() }}
        </div>
        <div class="top-row-btn">
            <a href="{{ route('backend.actions.create') }}" class="btn btn-outline-primary">
                Добавить акцию
            </a>
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $actions->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Название</th>
                    <th>Сортировка</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($actions as $action)
                    <tr>
                        <td>
                            {{ $action->id }}
                        </td>
                        <td>
                            {{ $action->name }}
                        </td>
                        <td>
                            {{ $action->sort }}
                        </td>
                        <td>
                            <a href="{{ route('backend.actions.edit', ['actionId' => $action->id]) }}">
                                Ред.
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Акции не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $actions->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
