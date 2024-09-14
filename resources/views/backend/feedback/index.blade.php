@extends('layouts.backend')

@section('pagetitle', 'Обратная связь')
@section('aside_feedback', 'active')

@section('content')

    <a href="{{ route('backend.shop.index') }}" class="back-link">
        Интернет-магазин
    </a>

    <div class="pagetitle">
        Обратная связь
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="top-row">
        <div class="top-row-text">
            Найдено обращений: {{ $feedbacks->total() }}
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
            {!! $feedbacks->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Создан</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($feedbacks as $feedback)
                    <tr>
                        <td>
                            {{ $feedback->id }}
                        </td>
                        <td>
                            {{ $feedback->name }}
                        </td>
                        <td>
                            {{ $feedback->phone }}
                        </td>
                        <td>
                            {{ $feedback->created_at->format('d.m.Y H:i:s') }}
                        </td>
                        <td>
                            <a href="{{ route('backend.feedback.show',['id' => $feedback->id]) }}">
                                Посмотреть
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Обращения не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $feedbacks->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
