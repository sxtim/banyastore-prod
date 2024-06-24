@extends('layouts.app')

@section('pagetitle', 'Заказ '.$order->id.' | Banyastore')

@section('content')
    {{ Breadcrumbs::render('personal') }}
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
                            @@include('blocks/card-product-order.html')
                            @@include('blocks/card-product-order.html')
                        </div>
                        <div class="order-detail-total">
                            <table class="order-detail-total__table">
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>Общая сумма</span></span></td>
                                    <td class="order-detail-total__cell-value">{{ number_format($order->price, 2, '.', ' ') }} ₽</td>
                                </tr>
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>Доставка</span></span></td>
                                    <td class="order-detail-total__cell-value">Бесплатно</td>
                                </tr>
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>Скидка</span></span></td>
                                    <td class="order-detail-total__cell-value">-30 000 ₽</td>
                                </tr>
                                <tr class="order-detail-total__row">
                                    <td class="order-detail-total__cell-title"><span
                                            class="order-detail-total__cell-decor"><span>ИТОГО</span></span></td>
                                    <td class="order-detail-total__cell-value">{{ number_format($order->price, 2, '.', ' ') }} ₽</td>
                                </tr>
                            </table>
                            <a class="btn-medium btn order-detail-total__btn"
                               href="{{ route('personal.orders.pay', ['id' => $order->id]) }}"
                            >
                                Оплатить
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
