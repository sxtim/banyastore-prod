@extends('layouts.backend')

@section('pagetitle', 'Заказ')
@section('aside_orders', 'active')

@section('content')

    <a href="{{ route('backend.order.index') }}" class="back-link">
        Зааказы
    </a>

    <div class="pagetitle">
        Заказ #{{ $order->id }}
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    <div class="row" id="backend-order">
        <div class="col-6">
            <table class="table table-wh table-hover table-bordered">
                <tr>
                    <td colspan="2" class=" text-center">
                        Общие данные
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Пользователь:</td>
                    <td>
                        <update-user-order-component
                            :link="'{{ route('backend.order.update-user', ['id' => $order->id]) }}'"
                            :default-user-name="'{{ $order->user ? $order->user->getFullName() : '' }}'"
                        ></update-user-order-component>
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Статус:</td>
                    <td>
                        <update-status-order-component
                            :default-status-id="{{ $order->status->id }}"
                            :statuses='@json($statuses)'
                            :link="'{{ route('backend.order.update-status', ['id' => $order->id]) }}'"
                        ></update-status-order-component>
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Имя:</td>
                    <td>
                        {{ $order->name }}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Телефон:</td>
                    <td>
                        {{ $order->phone }}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Почта:</td>
                    <td>
                        {{ $order->mail }}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Вариант оплаты:</td>
                    <td>
                        {{ $order->paymentVariant->name }}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right">Вариант доставки:</td>
                    <td>
                        {{ $order->deliveryVariant->name }}
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Создан:</td>
                    <td>{{ $order->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="text-right">Изменен:</td>
                    <td>{{ $order->updated_at->format('d.m.Y H:i:s') }}</td>
                </tr>
            </table>
        </div>

        <div class="col-6">
            <update-price-order-component
                :positions='@json($order->listProducts())'
                :link="'{{ route('backend.order.update-price', ['id' => $order->id]) }}'"
            ></update-price-order-component>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
