@extends('layouts.app')

@section('pagetitle', 'Регистрация | Banyastore')

@section('content')
    <section class="registration section">
        <div class="container">
            <div class="registration__title-container">
                <h1 class="registration__title title-s">РЕГИСТРАЦИЯ</h1>
            </div>
            <div class="reg__form-wrapper" id="register-form">
                <register-component></register-component>
            </div>
        </div>
    </section>
@endsection
