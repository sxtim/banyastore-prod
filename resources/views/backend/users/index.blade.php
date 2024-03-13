@extends('layouts.backend')

@section('pagetitle', 'Интернет-магазин')
@section('aside_users', 'active')

@section('content')

    <a href="{{ route('backend.index') }}" class="back-link">
        Панель управления
    </a>

    <div class="pagetitle">
        Пользователи
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="top-row">
        <div class="top-row-text">
            Найдено пользователей: {{ $users->total() }}
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
            {!! $users->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Дата регистрации</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            {{ $user->id }}
                        </td>
                        <td>
                            {{ $user->surname }} {{ $user->name }}
                        </td>
                        <td>
                            {{ $user->phone }}
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            {{ $user->created_at ? $user->created_at->format('d.m.Y H:i:s') : '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Пользователи не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $users->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>


@endsection

@section('scripts')

@endsection
