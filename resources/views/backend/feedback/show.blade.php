@extends('layouts.backend')

@section('pagetitle', 'Обращение')
@section('aside_feedback', 'active')

@section('content')

    <a href="{{ route('backend.feedback.index') }}" class="back-link">
        Обратная связь
    </a>

    <div class="pagetitle">
        Обращение #{{ $feedback->id }}
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="row" id="backend-order">
        <div class="col-12">
            <table class="table table-wh table-hover table-bordered">
                <tr>
                    <td colspan="2" class=" text-center">
                        Общие данные
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Создан:</td>
                    <td>{{ $feedback->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class=" text-right">Имя пользователя:</td>
                    <td>
                        {{ $feedback->name }}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Телефон пользователя:</td>
                    <td>
                        {{ $feedback->phone }}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Текст обращения:</td>
                    <td>
                        {{ $feedback->mess }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
