@extends('layouts.app')

@section('pagetitle', 'Ваш заказ успешно оплачен | Banyastore')

@section('content')
<section class="section">
    <div class="container">
        <div class="successful-order">
            <div class="successful-order__wrapper">
                <h1 class="successful-order__title title-s">Что-то пошло не так!</h1>
                <p class="successful-order__subtitle">Обратитесь к администратору сайта или попробуйте снова.</p>
                <a href="{{ route('home') }}" class="btn btn-medium btn-successful-order">Вернуться на главную</a>
            </div>
        </div>

    </div>
</section>
@endsection
