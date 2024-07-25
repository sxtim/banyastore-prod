@extends('layouts.app')

@section('pagetitle', 'Личный кабинет | Banyastore')

@section('content')
    {{ Breadcrumbs::render('personal') }}
    <section class="login-section section">
        <div class="container">
            <div class="login-section__title-container">
                <h1 class="login-section__title title-s">Вход</h1>
                <p class="login-section__subtitle">Пожалуйста, авторизуйтесь</p>
            </div>

{{--            OLD email auth --}}

{{--            <div class="login-section__auth-form">--}}
{{--                <form action="{{ route('login') }}" method="POST">--}}
{{--                    @csrf--}}
{{--                    <div class="label-box">--}}
{{--                        <label class="auth-form__login">Email</label>--}}
{{--                    </div>--}}
{{--                    <div class="input-box">--}}
{{--                        <input type="email" name="email" required/>--}}
{{--                    </div>--}}
{{--                    <div class="label-box">--}}
{{--                        <label class="auth-form__email">Пароль</label>--}}
{{--                    </div>--}}
{{--                    <div class="input-box">--}}
{{--                        <input type="password" name="password" required/>--}}
{{--                    </div>--}}
{{--                    <!--        <div class="label-box">-->--}}
{{--                    <!--          <label class="i'm not robot">I'm not Robot</label>-->--}}
{{--                    <!--        </div>-->--}}
{{--                    <!--        <div class="input-check-box">-->--}}
{{--                    <!--          <input type="checkbox" name="I'm not Robot" id="I'm not Robot" required/>-->--}}
{{--                    <!--        </div>-->--}}
{{--                    <div class="auth-form__remember">--}}
{{--                        <label>--}}
{{--                            <input class="auth-form__remember-checkbox real-checkbox" type="checkbox">--}}
{{--                            <span class="custom-checkbox"></span>--}}
{{--                            Запомнить меня на этом компьютере--}}
{{--                        </label>--}}
{{--                    </div>--}}
{{--                    <button class="auth-form__button btn ">Войти</button>--}}
{{--                    <a class="auth-form__forgot" href="#!">Забыли свой пароль?</a>--}}
{{--                    <div class="auth-form__reg-box">--}}
{{--                        <p class="auth-form__reg-text">Если Вы впервые на сайте, заполните, пожалуйста, регистрационную форму.</p>--}}
{{--                        <a href="#!" class="auth-form__reg-link btn btn-white">Зарегистрироваться</a>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
            <div class="auth-form__wrapper">
                <form class="auth-form form-container" action="">
                    <div class="label-box">
                        <label for="user-tel">Телефон</label>
                    </div>
                    <div class="input-box auth-form__phone">
                        <input class="form-phone" type="tel" name="tel" id="user-tel" axlength="12" required/>
                    </div>
                    <div class="label-box">
                        <label for="input-sms-pass">Код из СМС</label>
                    </div>
                    <div class="input-box">
                        <input id="input-sms-pass" type="password" name="user-password" placeholder="" minlength="8" required />
                        <button class="auth-form__btn-sms">Получить код</button>
                    </div>

                    <button class="auth-form__button btn ">Войти</button>
                    <span class="auth-form__reg-text">Указывая номер телефона, вы принимаете условия
          <br>
          <a href="#!">пользовательского соглашения</a></span>
                    <div class="auth-form__reg-box">
                        <p class="auth-form__reg-text">Если Вы впервые на сайте, заполните, пожалуйста, регистрационную форму.</p>
                        <a href="signup-old.html" class="auth-form__reg-link btn btn-white">Зарегистрироваться</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
