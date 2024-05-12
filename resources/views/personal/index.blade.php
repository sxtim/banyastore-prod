@extends('layouts.app')

@section('pagetitle', 'Личный кабинет | Banyastore')

@section('content')
    {{ Breadcrumbs::render('personal') }}
    <section class="lk section">
        <div class="container">
            <h1 class="lk__title title-s">Личный кабинет</h1>
            <div class="lk__container">

                <nav class="lk__nav">
                    <ul class="lk__nav-item"><a class="btn-lk-nav btn" href="{{ route('personal.index') }}">Личные данные</a></ul>
                    <ul class="lk__nav-item"><a class="btn-lk-nav btn" href="#!">Заказы</a></ul>
                    <ul class="lk__nav-item"><a class="btn-lk-nav btn" href="#!">Избранное</a></ul>
                    <ul class="lk__nav-item"><a class="btn-lk-nav btn btn-lk-nav-active" href="{{ route('basket.index') }}">Корзина</a></ul>
                </nav>

                <div class="user-data">
                    <h2 class="user-data__title title-form">Личные данные</h2>
                    <div class="user-data__form-container">
                        <form class="user-data__form user-form">
                            <label class="user-form__name">Имя</label>
                            <input type="text" name="name" placeholder="" minlength="3" axlength="20" required value="{{ $user->name }}"/>
                            <label class="user-form__phone">Телефон</label>
                            <input type="tel" name="reference" id="user-form-tel" placeholder="" axlength="12" required value="{{ $user->phone }}"/>
                            <label class="user-form__email">Почта</label>
                            <input type="email" name="email" placeholder="" required value="{{ $user->mail }}"/>
                            <button class="user-form-btn btn btn-medium" type="submit">Сохранить</button>
                        </form>
                    </div>
                    <form class="user-data__form user-form">
                        <h2 class="user-data__title title-form">Изменение пароля</h2>
                        <label class="user-form__pass">Новый пароль</label>
                        <input type="text" name="pass" id="user-form-pass" placeholder="" minlength="3" axlength="20" required/>
                        <label class="user-form__newpass">Подтвердите новый пароль</label>
                        <input type="text" name="newpass" id="user-form-newpass" placeholder="" axlength="12" required/>
                        <button class="user-form-btn btn btn-medium" type="submit">Изменить</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
