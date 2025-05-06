@extends('layouts.app')

@section('pagetitle', 'Заказ '.$order->id.' | Banyastore')

@section('content')
    {{ Breadcrumbs::render('order-detail', $order) }}
    <section class="lk section">
        <div class="container">
            <h1 class="lk__title title-s">Личный кабинет</h1>
            <div class="lk__container">

                @include('blocks/menu-personal')

                <div class="order-detail">
                    <h2>
                        <a class="title-form order-detail-title" href="{{ route('personal.orders.detail', ['id' => $order->id]) }}">
                            К списку заказов
                        </a>
                    </h2>
                    <div class="order-detail__wrapper">
                        @include('blocks/card-order')

                        <div class="cards-product-order__wrapper">
                            <h2 class="title-form cards-product-order-title">Товары в заказе</h2>
                            @foreach($products as $product)
                                @include('blocks/card-product-order')
                            @endforeach
                        </div>
                        <div class="order-detail-total">
                            <table class="order-detail-total__table">
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>Общая сумма</span></span></td>
                                    <td class="order-detail-total__cell-value">{{ number_format($order->totalBasePrice(), 2, '.', ' ') }} ₽</td>
                                </tr>
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>Доставка</span></span></td>
                                    <td class="order-detail-total__cell-value">Бесплатно</td>
                                </tr>
                                @if ($order->totalDiscount() > 0)
                                    <tr class="order-detail-total__row">
                                        <td class="order-detail-total__cell-title"><span
                                                class="order-detail-total__cell-decor"><span>Скидка</span></span></td>
                                        <td class="order-detail-total__cell-value">
                                            -{{ number_format($order->totalDiscount(), 2, '.', ' ') }} ₽
                                        </td>
                                    </tr>
                                @endif
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>ИТОГО</span></span></td>
                                    <td class="order-detail-total__cell-value">{{ number_format($order->price, 2, '.', ' ') }} ₽</td>
                                </tr>
                            </table>
                            @if ($order->status->value_status === 30)
                                <a class="btn-medium btn order-detail-total__btn"
                                   href="{{ route('personal.orders.pay', ['id' => $order->id]) }}"
                                >
                                    Оплатить
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
