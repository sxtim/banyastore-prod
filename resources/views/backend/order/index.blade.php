@extends('layouts.backend')

@section('pagetitle', 'Заказы')
@section('aside_orders', 'active')

@section('content')

    <a href="{{ route('backend.shop.index') }}" class="back-link">
        Интернет-магазин
    </a>

    <div class="pagetitle">
        Заказы
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
            Найдено заказов: {{ $orders->total() }}
        </div>
    </div>

    <div class="table-row-modif">
        <div class="table-elem-modif">
{{--            @include('backend.shop.parts.tabs')--}}
            {!! $orders->appends(Request::except('page'))->links('backend.pagination') !!}
            <table class="table table-hover table-striped table-bordered" style="font-size: 13px">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Статус</th>
                    <th>Пользователь</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Почта</th>
                    <th>Вариант оплаты</th>
                    <th>Вариант доставки</th>
                    <th>Создан</th>
                    <th style="width: 50px">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            {{ $order->id }}
                        </td>
                        <td>
                            {{ $order->status->name }}
                        </td>
                        <td>
                            @if ($order->user)
                                {{ $order->user->name }}
                            @endif
                        </td>
                        <td>
                            {{ $order->name }}
                        </td>
                        <td>
                            {{ $order->phone }}
                        </td>
                        <td>
                           {{ $order->mail }}
                        </td>
                        <td>
                            {{ $order->paymentVariant->name }}
                        </td>
                        <td>
                            {{ $order->deliveryVariant->name }}
                        </td>
                        <td>
                            {{ $order->created_at->format('d.m.Y H:i:s') }}
                        </td>
                        <td>
                            <a href="{{ route('backend.order.show',['id' => $order->id]) }}">
                                Посмотреть
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            Заказы не найдены
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $orders->appends(Request::except('page'))->links('backend.pagination') !!}
        </div>
    </div>
@endsection

@section('scripts')
@endsection
