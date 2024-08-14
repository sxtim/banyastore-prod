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

            <div id="login-form">
                <login-component></login-component>
            </div>

        </div>
    </section>
@endsection
