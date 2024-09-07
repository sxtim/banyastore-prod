@extends('layouts.app')

@section('pagetitle', 'Обратная связь | Banyastore')

@section('content')
    <div class="container">
        <section class="feedback section">
            <div class="center-title-container">
                <h1 class="center-title title-s">Заказать звонок</h1>
            </div>
            <div class="auth-form__wrapper">
                <form class="auth-form form-container">
                    <div class="label-box">
                        <label for="user-name">Имя</label>
                    </div>
                    <div class="input-box">
                        <input id="user-name" type="text" name="user-name" placeholder="" v-model="name"/>
                    </div>
                    <div class="label-box">
                        <label for="user-tel">Телефон</label>
                    </div>
                    <div class="input-box auth-form__phone">
                        <input v-model="phone" class="form-phone" type="tel" name="tel" id="user-tel" axlength="12"/>
                    </div>
                    <div class="label-box">
                        <label for="feedback-text">Введите сообщение</label>
                    </div>
                    <div>
                    <textarea class="input-box-text" id="feedback-text" wrap="soft" name="feedback-text"
                              placeholder=""></textarea>
                    </div>
                    <div class="auth-form__button btn">Отправить</div>
                    <span class="auth-form__reg-text">Указывая номер телефона, вы принимаете условия
          <br>
          <a href="#!">пользовательского соглашения</a></span>
                </form>
            </div>
        </section>
    </div>
@endsection
