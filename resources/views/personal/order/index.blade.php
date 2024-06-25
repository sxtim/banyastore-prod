@extends('layouts.app')

@section('pagetitle', 'Заказы | Banyastore')

@section('content')
    {{ Breadcrumbs::render('orders') }}
    <section class="lk section">
        <div class="container">
            <h1 class="lk__title title-s">Личный кабинет</h1>
            <div class="lk__container">

                @include('blocks/menu-personal')

                <div class="orders">
                    <h2 class=" title-form">Заказы</h2>
                    <div class="orders__wrapper-card">
                        @foreach($orders as $order)
                            @include('blocks/card-order')
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
