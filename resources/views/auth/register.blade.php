@extends('layouts.app')

@section('pagetitle', 'Личный кабинет | Banyastore')

@section('content')
    <section class="registration section">
        <div class="container">
            <div class="registration__title-container">
                <h1 class="registration__title title-s">РЕГИСТРАЦИЯ</h1>
            </div>
            <div class="reg__form-wrapper">
                <form id="reg-form" action="" class="reg-form form-container">
                    <div class="label-box">
                        <label for="user-name">Имя</label>
                    </div>
                    <div class="input-box">
                        <input id="user-name" type="text" name="user-name" placeholder="" minlength="3" axlength="20" required/>
                    </div>
                    <div class="label-box">
                        <label for="user-tel">Телефон</label>
                    </div>
                    <div class="input-box">
                        <input class="form-phone" id="user-tel" type="tel" name="tel" id="user-tel" axlength="12" required/>
                    </div>
                    <div class="label-box">
                        <label for="user-email">Электронная почта</label>
                    </div>
                    <div class="input-box">
                        <input id="user-email" type="email" name="email" placeholder="" required/>
                    </div>
                    <button class="reg-form__btn-reg btn" >Зарегистироваться</button>
                    <span class="input-box__msg">Пользователь с таким телефоном уже зарегистрирован</span>
                    <p class="registration-form__text">Нажимая кнопку «Зарегистрироваться», я даю свое согласие на обработку моих
                        персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных»,
                        на условиях и для целей, определенных в Согласии на обработку персональных данных</p>
                    <a href="signin-old.html" class="registration-form__log-link btn-white btn">Авторизация</a>
                </form>
            </div>
        </div>
    </section>
@endsection
